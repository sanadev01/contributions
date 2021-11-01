<?php


namespace App\Repositories;


use Stripe\Charge;
use Stripe\Stripe;
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
use Illuminate\Database\Eloquent\Model;
use App\Services\PaymentServices\AuthorizeNetService;

class DepositRepository
{
    protected $error;
    protected $fileName;
    public function get(Request $request,$paginate = true,$pageSize=50,$orderBy = 'id',$orderType='asc')
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
                            ->orWhere('email','LIKE',"%{$request->user}%");
            });
        }

        if ( $request->warehouseNumber ){
            $query->whereHas('orders',function($query) use($request){
                return $query->where('warehouse_number','LIKE',"%{$request->warehouseNumber}%");
            });
        }

        if ( $request->trackingCode ){
            $query->whereHas('orders',function($query) use($request){
                return $query->where('corrios_tracking_code','LIKE',"%{$request->trackingCode}%");
            });
        }

        if ( $request->type ){
            $query->where('is_credit',$request->type);
        }

        if ( $request->uuid ){
            $query->where('uuid','LIKE',"%{$request->uuid}%");
        }

        if ( $request->dateFrom ){
            $query->where('created_at','>=',$request->dateFrom. ' 00:00:00');
        }

        if ( $request->dateTo ){
            $query->where('created_at','<=',$request->dateTo. ' 23:59:59');
        }


        if ( $request->last_four_digits ){
            $query->where('last_four_digits','LIKE',"%{$request->last_four_digits}%");
        }

        if ( $request->description ){
            $query->where('description','LIKE',"%{$request->description}%");
        }

        if ( $request->balance ){
            $query->where('balance','LIKE',"%{$request->balance}%");
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
                    'card_no' => $request->card_no,
                    'expiration' => $request->expiration,
                    'cvv' => $request->cvv,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'state' => State::find($request->state)->code,
                    'zipcode' => $request->zipcode,
                    'country' => Country::find($request->country)->name
                ]);
            }

            if ( $request->has('save-address') ){
                $billingInformation->save();
            }

            if($paymentGateway == 'STRIPE')
            {
                $transactionID = PaymentInvoice::generateUUID('DP-');
                $this->stripePayment($request);

                if($this->error != null)
                {
                    DB::rollBack();
                    return false;
                }
            }

            if($paymentGateway == 'AUTHORIZE')
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

            Deposit::create([
                'uuid' => $transactionID,
                'transaction_id' => ($paymentGateway == 'STRIPE') ? null : $response->data->getTransId(),
                'amount' => $request->amount,
                'user_id' => Auth::id(),
                'balance' => Deposit::getCurrentBalance() + $request->amount,
                'is_credit' => true,
                'last_four_digits' => substr($billingInformation->card_no,-4)
            ]);

            DB::commit();

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
            Charge::create ([
                'amount' => (float)$request->amount * 100,
                'currency' => "usd",
                'source' => $request->stripe_token,
                'description' => "User paid to HomeDelivery"
            ]);

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
        // dd($request->attachment);
        // if ($request->has('attachment')) {
            
        //     $this->fileName = time().'.'.$request->attachment->extension();
        //     $request->attachment->storeAs('deposits', $this->fileName);
        // }

        
        $deposit = Deposit::create([
            'uuid' => PaymentInvoice::generateUUID('DP-'),
            'amount' => $request->amount,
            'user_id' => $request->user_id,
            'balance' => $balance + $request->amount,
            'is_credit' => true,
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
    }

    public function getError()
    {
        return $this->error;
    }
    
}
