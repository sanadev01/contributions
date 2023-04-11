<?php


namespace App\Repositories;


use Stripe\Charge;
use Stripe\Stripe;
use Carbon\Carbon;
use Stripe\Customer;
use App\Models\Order;
use App\Models\State;
use App\Models\Country;
use App\Models\Deposit;
use App\Models\Document;
use App\Events\OrderPaid;
use Illuminate\Http\Request;
use App\Mail\User\PaymentPaid;
use App\Models\PaymentInvoice;
use App\Models\BillingInformation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Mail\Admin\NotifyTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Services\PaymentServices\AuthorizeNetService;

class DepositRepository
{
    protected $error;
    protected $fileName;
    protected $chargeID;

    public function get(Request $request,$paginate = true,$pageSize=50,$orderBy = 'id',$orderType='asc')
    {
        $query = Deposit::query();

        if ($paginate == false) {
            $query->with('orders');
        }

        if ( !Auth::user()->isAdmin() ){
            $query->where('user_id',Auth::id());
        }

        if ( $request->user ){
            $query->whereHas('user',function($query) use($request) {
                return $query->where('pobox_number',"%{$request->user}%")
                            ->orWhere('name','LIKE',"%{$request->user}%")
                            ->orWhere('last_name','LIKE',"%{$request->user}%")
                            ->orWhere('email','LIKE',"%{$request->user}%")
                            ->orWhere('id', $request->user);
            });
        }

        if ( $request->filled('warehouseNumber') ){
            $query->where('order_id','LIKE',"%{$request->warehouseNumber}%");
        }

        if ( $request->filled('trackingCode') ){
            $query->whereHas('orders',function($query) use($request){
                return $query->where('corrios_tracking_code','LIKE',"%{$request->trackingCode}%");
            });
        }

        if ( $request->filled('type') ){
            $query->where('is_credit',$request->type);
        }

        if ( $request->filled('uuid') ){
            $query->where('uuid','LIKE',"%{$request->uuid}%");
        }

        if ( $request->filled('dateFrom') ){
            $query->where('created_at','>=',$request->dateFrom. ' 00:00:00');
        }

        if ( $request->filled('dateTo') ){
            $query->where('created_at','<=',$request->dateTo. ' 23:59:59');
        }


        if ( $request->filled('last_four_digits') ){
            $query->where('last_four_digits','LIKE',"%{$request->last_four_digits}%");
        }

        if ( $request->filled('description') ){
            $query->where('description','LIKE',"%{$request->description}%");
        }

        if ( $request->filled('balance') ){
            $query->where('balance','LIKE',"%{$request->balance}%");
        }

        if ( $request->filled('card') ){
            $query->where('last_four_digits','LIKE',"%{$request->card}%");
        }

        $query->orderBy($orderBy,$orderType);
        $query->latest('id');

        return $paginate ? $query->paginate($pageSize) : $query->get(); 
    }

    public function store(Request $request)
    {
        $paymentGateway = setting('PAYMENT_GATEWAY', null, null, true);
        
        DB::beginTransaction();

        try {

            $billingInformation = null;

            if ( $request->billingInfo ){
                $billingInformation = BillingInformation::find($request->billingInfo);
            }

            if ( !$billingInformation ){
                $billingInformation = new BillingInformation([
                    'user_id' => Auth::id(),
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'expiration' => ($request->payment_gateway == 'stripe_ach') ? null : $request->expiration,
                    'cvv' => ($request->payment_gateway == 'stripe_ach') ? $request->routing_number : $request->cvv,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'state' => State::find($request->state)->code,
                    'zipcode' => $request->zipcode,
                    'country' => Country::find($request->country)->name,
                    'card_no' => ($request->payment_gateway == 'stripe_ach') ? $request->account_no : $request->card_no,
                ]);
            }

            if ( $request->has('save-address') ){
                $billingInformation->save();
            }

            if($request->payment_gateway == 'stripe')
            {
                $transactionID = PaymentInvoice::generateUUID('DP-');
                $this->stripePayment($request);

                if($this->error != null)
                {
                    DB::rollBack();
                    return false;
                }
            }

            if($request->payment_gateway == 'stripe_ach')
            {
                $transactionID = PaymentInvoice::generateUUID('DP-');
                $this->stripeAchPayment($request);

                if($this->error != null)
                {
                    DB::rollBack();
                    return false;
                }
            }

            if($request->payment_gateway == 'authorize')
            {
                $authorizeNetService = new AuthorizeNetService();

                $transactionID = PaymentInvoice::generateUUID('DP-');
                $response = $authorizeNetService->makeCreditCardPaymentWithoutInvoice($billingInformation,$transactionID,$request->amount,Auth::user());


                if ( !$response->success ){
                    $this->error = json_encode($response->message);
                    DB::rollBack();
                    return false;
                }
            }
            $user = Auth::user()->name;
            $deposit = Deposit::create([
                'uuid' => $transactionID,
                'transaction_id' => ($request->payment_gateway == 'stripe' || $request->payment_gateway == 'stripe_ach') ? $this->chargeID : $response->data->getTransId(),
                'amount' => $request->amount,
                'user_id' => Auth::id(),
                'balance' => Deposit::getCurrentBalance() + $request->amount,
                'is_credit' => true,
                'last_four_digits' => substr($billingInformation->card_no,-4)
            ]);

            DB::commit();

            //SendMailNotification
            $this->sendTransactionMail($deposit, $user);

            return true;

        } catch (\Exception $ex) {
            DB::rollBack();
            $this->error = $ex->getMessage();
            return false;
        }
    }

    private function stripePayment($request)
    {
        $stripeSecret = setting('STRIPE_SECRET', null, null, true);
        
        Stripe::setApiKey($stripeSecret);
        try {
            $charge =Charge::create ([
                'amount' => (float)$request->amount * 100,
                'currency' => "usd",
                'source' => $request->stripe_token,
                'description' => auth()->user()->pobox_number.' '.'charged HD account',
            ]);

            $this->chargeID = $charge->id;
            return true;

        } catch (\Exception $ex) {
            $this->error = $ex->getMessage();

            return false;
        }
    }

    private function stripeAchPayment($request)
    {
        $stripeSecret = setting('STRIPE_SECRET', null, null, true);
        
        Stripe::setApiKey($stripeSecret);

        try {

            $customer = Customer::create([
                'description' => $request->first_name . ' ' . $request->last_name,
                'source' => $request->stripe_token,
            ]);

            if($this->verifyCustomer($customer, $request))
            {
                return true;
            }

        } catch (\Exception $th) {
            $this->error = $th->getMessage();

            return false;
        }
        
    }

    private function verifyCustomer($customer, $request)
    {
        try {

            $bank_account = Customer::retrieveSource(
                $customer->id,
                $customer->default_source
            );

            $bank_account->verify(['amounts' => [32, 45]]);

            if($this->stripeAchCharge($customer, $request))
            {
                return true;
            }

        } catch (\Exception $ex) {

            $this->error = $ex->getMessage();

            return false;
        }
        
    }

    private function stripeAchCharge($customer, $request)
    {
        try {

            $stripeSecret = setting('STRIPE_SECRET', null, null, true);

            $stripe = new \Stripe\StripeClient($stripeSecret);

            $charge = $stripe->charges->create([
                'amount' => (float)$request->amount * 100, 
                'currency' => 'usd', 
                'customer' => $customer->id,
            ]);

            $this->chargeID = $charge->id;
            return true;

        } catch (\Exception $ex) {
            return $this->error = $ex->getMessage();
        }
    }


    public function adminAdd(Request $request)
    {
        $lastTransaction = Deposit::query()->where('user_id',$request->user_id)->latest('id')->first();
        if ( !$lastTransaction ){
            $balance =  0;
        }else{
            $balance = $lastTransaction->balance;
        }
        // if ($request->has('attachment')) {
            
        //     $this->fileName = time().'.'.$request->attachment->extension();
        //     $request->attachment->storeAs('deposits', $this->fileName);
        // }

        $deposit = Deposit::create([
            'uuid' => PaymentInvoice::generateUUID('DP-'),
            'amount' => $request->amount,
            'user_id' => $request->user_id,
            'balance' => ($request->is_credit == "true") ? $balance + $request->amount : $balance - $request->amount,
            'is_credit' =>  ($request->is_credit == "true") ? true : 0,
            'last_four_digits' => Auth::user()->name,
            'attachment' => $this->fileName,
            'description' => $request->description,
        ]);

        if ($request->hasFile('attachment')) {
            foreach ($request->file('attachment') as $attach) {
                $document = Document::saveDocument($attach);
                $deposit->depositAttchs()->create([
                    'name' => $document->getClientOriginalName(),
                    'size' => $document->getSize(),
                    'type' => $document->getMimeType(),
                    'path' => $document->filename
                ]);
            }
        }
        $user = Auth::user()->name;

        //SendMailNotification
        $this->sendTransactionMail($deposit, $user);
    }

    public function getError()
    {
        return $this->error;
    }

    private function sendTransactionMail($deposit, $user){
        try {
            \Mail::send(new NotifyTransaction($deposit, null, $user));
        } catch (\Exception $ex) {
            \Log::info('Notify Transaction email send error: '.$ex->getMessage());
        }
    }

    public function getLiability(Request $request,$paginate = true,$pageSize=50,$orderBy = 'id',$orderType='DESC')
    {
        $query = Deposit::query();

        if ( !Auth::user()->isAdmin() ){
            $query->where('user_id',Auth::id());
        }

        if ( $request->user ){
            $query->whereHas('user',function($query) use($request) {
                return $query->where('pobox_number',"%{$request->user}%")
                            ->orWhere('name','LIKE',"%{$request->user}%")
                            ->orWhere('last_name','LIKE',"%{$request->user}%")
                            ->orWhere('email','LIKE',"%{$request->user}%")
                            ->orWhere('id', $request->user);
            });
        }
        if ( $request->poboxNumber ){
            $query->whereHas('user',function($query) use($request) {
                return $query->where('pobox_number',"%{$request->poboxNumber}%")
                            ->orWhere('name','LIKE',"%{$request->poboxNumber}%")
                            ->orWhere('last_name','LIKE',"%{$request->poboxNumber}%")
                            ->orWhere('email','LIKE',"%{$request->poboxNumber}%")
                            ->orWhere('id', $request->poboxNumber);
            });
        }

        if ( $request->filled('dateFrom') ){
            $query->where('created_at','>=',$request->dateFrom. ' 00:00:00');
        }

        if ( $request->filled('dateTo') ){
            $query->where('created_at','<=',$request->dateTo. ' 23:59:59');
        }

        if ( $request->filled('balance') ){
            $query->where('balance','LIKE',"%{$request->balance}%");
        }
        $query->groupBy('user_id');
        $query->orderBy($orderBy,'DESC');
        $query->latest();

        return $paginate ? $query->paginate($pageSize) : $query->get(); 
    }
    
    public function getUserLiability(Request $request,$paginate = true,$pageSize=50,$orderBy = 'id',$orderType='DESC')
    {
       return User::with(['deposits' => function ($q) use ($orderType){
            $q->orderBy('balance',$orderType);
         }])->whereHas('deposits')
       ->when(!Auth::user()->isAdmin(),function($query) {
            $query->where('id',Auth::id());
        })->whereHas('deposits',function($query) use($request){
                $query->when($request->balance, function($query,$balance){
                         $query->where('balance','LIKE',"%{$balance}%");
                    })->when($request->dateTo, function($query,$dateTo){
                        $query->where('created_at','<=',$dateTo. ' 23:59:59');
                    })->when($request->dateFrom, function($query,$dateFrom){
                        return $query->where('created_at','>=',$dateFrom. ' 00:00:00');
                    });
             })->when($request->user,function($query ,$user){
                return $query->where('pobox_number',"%{$user}%")
                            ->orWhere('name','LIKE',"%{$user}%")
                            ->orWhere('last_name','LIKE',"%{$user}%")
                            ->orWhere('email','LIKE',"%{$user}%")
                            ->orWhere('id', $user);
        })->when($request->poboxNumber,function($query ,$poboxNumber){
                return $query->where('pobox_number',"%{$poboxNumber}%")
                            ->orWhere('name','LIKE',"%{$poboxNumber}%")
                            ->orWhere('last_name','LIKE',"%{$poboxNumber}%")
                            ->orWhere('email','LIKE',"%{$poboxNumber}%")
                            ->orWhere('id', $poboxNumber);
            })->when($orderBy !='balance', function($query) use($orderBy,$orderType){
                return $query->orderBy($orderBy,$orderType);
              })->latest()
             ->when($paginate,function($query) use($pageSize) {
                return $query->paginate($pageSize);
             })->when(!$paginate,function($query) use($pageSize) {
                return $query->get();
             });
    }
    
}
