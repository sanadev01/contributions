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
use App\Events\OrderPaid;
use App\Mail\User\PaymentPaid;
use App\Models\PaymentInvoice;
use App\Models\BillingInformation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\Admin\NotifyTransaction;
use App\Services\PaymentServices\AuthorizeNetService;

class OrderCheckoutRepository
{
    public $invoice;
    public $request;
    protected $error;

    public function handle($invoice, $request)
    {
        $this->invoice = $invoice;
        $this->request = $request;

        if ($invoice->differnceAmount()) {
            return $this->payUpdatedInvoice();
        }

        return $this->payInvoice();
    }

    private function payInvoice()
    {
        if($this->request->pay){

            if(getBalance() < $this->invoice->total_amount){
                session()->flash('alert-danger','Not Enough Balance. Please Recharge your account.');
                return back();
            }
            
            DB::beginTransaction();
            $user = Auth::user()->name;
            try {
                
                foreach($this->invoice->orders as $order){
                        $preStatus = $order->status_name;
                    if ( !$order->isPaid() &&  getBalance() >= $order->gross_total ){
                        $deposit = chargeAmount($order->gross_total,$order);
                    }
                }
                
                $this->invoice->update([
                    'is_paid' => true
                ]);
                
                $this->invoice->orders()->update([
                    'is_paid' => true,
                    'status' => Order::STATUS_PAYMENT_DONE
                ]);
                
                try {
                    \Mail::send(new NotifyTransaction($deposit, $preStatus, $user));
                } catch (\Exception $ex) {
                    \Log::info('Pay Invoice Notify Transaction email send error: '.$ex->getMessage());
                }
                DB::commit();
                AutoChargeAmountEvent::dispatch($this->invoice->orders()->first()->user);
            } catch (\Exception $ex) {
                DB::rollBack();
                session()->flash('alert-danger',$ex->getMessage());
                return back();
            }
            
        }

        if(!$this->request->pay){
            if (!$this->checkoutWithCard() ){
                session()->flash('alert-danger',$this->error);
                return back();
            }

            $this->invoice->update([
                'is_paid' => true,
                'paid_amount' => $this->invoice->orders()->sum('gross_total'),
            ]);

            $this->invoice->orders()->update([
                'is_paid' => true,
                'status' => Order::STATUS_PAYMENT_DONE
            ]);
        }

        event(new OrderPaid($this->invoice->orders, true));

        session()->flash('alert-success', __('orders.payment.alert-success'));
        return view('admin.payment-invoices.index');
    }

    private function payUpdatedInvoice()
    {
        
        if ($this->request->pay) {
            
            if(getBalance() < $this->invoice->differnceAmount()){
                session()->flash('alert-danger','Not Enough Balance. Please Recharge your account.');
                return back();
            }

            DB::beginTransaction();

                try {

                    $order = $this->invoice->orders->firstWhere('is_paid', false);
                    $description = 'Payment with invoice # '.$this->invoice->uuid.' for order # ';

                    chargeAmount($this->invoice->differnceAmount() ,$order ,$description);

                    $this->invoice->update([
                        'is_paid' => true,
                        'paid_amount' => $this->invoice->orders()->sum('gross_total'),
                    ]);

                    $this->invoice->orders()->update([
                        'is_paid' => true,
                        'status' => Order::STATUS_PAYMENT_DONE
                    ]);
                    DB::commit();

                } catch (\Exception $ex) {
                DB::rollBack(); 
                    session()->flash('alert-danger',$ex->getMessage());
                    return back();
                } 
        }

        if(!$this->request->pay){
            if (!$this->checkoutWithCard() ){
                session()->flash('alert-danger',$this->error);
                return back();
            }

            $this->invoice->update([
                'is_paid' => true,
                'paid_amount' => $this->invoice->orders()->sum('gross_total'),
            ]);

            $this->invoice->orders()->update([
                'is_paid' => true,
                'status' => Order::STATUS_PAYMENT_DONE
            ]);
        }


        event(new OrderPaid($this->invoice->orders, true));
        session()->flash('alert-success', __('orders.payment.alert-success'));
        return redirect()->route('admin.payment-invoices.index');
    }

    private function checkoutWithCard()
    {
        DB::beginTransaction();

        try {

            $billingInformation = null;

            if ( $this->request->billingInfo ){
                $billingInformation = BillingInformation::find($this->request->billingInfo);
            }

            if ( !$billingInformation ){
                $billingInformation = new BillingInformation([
                    'user_id' => Auth::id(),
                    'first_name' => $this->request->first_name,
                    'last_name' => $this->request->last_name,
                    'card_no' => ($this->request->payment_gateway == 'stripe_ach') ? $this->request->account_no : $this->request->card_no,
                    'expiration' => ($this->request->payment_gateway == 'stripe_ach') ? null : $this->request->expiration,
                    'cvv' => ($this->request->payment_gateway == 'stripe_ach') ? $this->request->routing_number : $this->request->cvv,
                    'phone' => $this->request->phone,
                    'address' => $this->request->address,
                    'state' => State::find($this->request->state)->code,
                    'zipcode' => $this->request->zipcode,
                    'country' => Country::find($this->request->country)->name
                ]);
            }

            if ( $this->request->has('save-address') ){
                $billingInformation->save();
            }

            if($this->request->payment_gateway == 'stripe')
            {
                $this->stripePayment();

                if($this->error != null)
                {
                    DB::rollBack();
                    return false;
                }
            }

            if($this->request->payment_gateway == 'stripe_ach')
            {
                $this->stripeAchPayment();

                if($this->error != null)
                {
                    DB::rollBack();
                    return false;
                }
            }

            if($this->request->payment_gateway == 'authorize')
            {
                $authorizeNetService = new AuthorizeNetService();

                $response = $authorizeNetService->makeCreditCardPayement($billingInformation,$this->invoice);

                if ( !$response->success ){
                    $this->error = json_encode($response->message);
                    DB::rollBack();
                    return false;
                }
            }
            
            if ( $response->success ){
                $this->invoice->update([ 
                    'last_four_digits' => substr($billingInformation->card_no,-4),
                    'is_paid' => true
                ]);
    
                $this->invoice->transactions()->create([
                    'transaction_id' => ($this->request->payment_gateway == 'stripe' || $this->request->payment_gateway == 'stripe_ach') ? $this->chargeID : $response->data->getTransId(),
                    'amount' => $this->invoice->total_amount
                ]);
    
                $this->invoice->orders()->update([
                    'is_paid' => true,
                    'status' => Order::STATUS_PAYMENT_DONE
                ]);
                
                try {
                    Mail::send(new PaymentPaid($this->invoice));
                } catch (\Exception $ex) {
                    Log::info('Payment Paid email send error: '.$ex->getMessage());
                }
    
                DB::commit();
    
                return true;
            }
            
        } catch (\Exception $ex) {
            DB::rollBack();
            $this->error = $ex->getMessage();
            return false;
        }
    }

    private function stripePayment()
    {
        $stripeSecret = setting('STRIPE_SECRET', null, null, true);
        $amountToPay = ($this->invoice->differnceAmount()) ? $this->invoice->differnceAmount() : $this->invoice->total_amount;

        Stripe::setApiKey($stripeSecret);
        try {
            $charge = Charge::create ([
                'amount' => (float)$amountToPay * 100,
                'currency' => "usd",
                'source' => $this->request->stripe_token,
                'description' => auth()->user()->pobox_number.' '.'paid to HomeDelivery against payment invoice# '.$this->Invoice->uuid,
            ]);
                        
            $this->chargeID = $charge->id;
            return true;


        } catch (\Exception $ex) {
            $this->error = $ex->getMessage();

            return false;
        }
    }

    private function stripeAchPayment()
    {
        $stripeSecret = setting('STRIPE_SECRET', null, null, true);
        
        Stripe::setApiKey($stripeSecret);

        $amountToPay = ($this->invoice->differnceAmount()) ? $this->invoice->differnceAmount() : $this->invoice->total_amount;

        try {

            $customer = Customer::create([
                'description' => $this->request->first_name . ' ' . $this->request->last_name,
                'source' => $this->request->stripe_token,
            ]);

            if($this->verifyCustomer($customer, $amountToPay))
            {
                return true;
            }

        } catch (\Exception $th) {
            $this->error = $th->getMessage();

            return false;
        }
        
    }

    private function verifyCustomer($customer, $amountToPay)
    {
        try {

            // get the existing bank account of customer
            $bank_account = Customer::retrieveSource(
                $customer->id,
                $customer->default_source
            );

            // verify the account(stripe default)
            $bank_account->verify(['amounts' => [32, 45]]);

            if($this->stripeAchCharge($customer, $amountToPay))
            {
                return true;
            }

        } catch (\Exception $ex) {

            $this->error = $ex->getMessage();

            return false;
        }
        
    }

    private function stripeAchCharge($customer, $amountToPay)
    {
        try {

            $stripeSecret = setting('STRIPE_SECRET', null, null, true);

            $stripe = new \Stripe\StripeClient($stripeSecret);

            $charge = $stripe->charges->create([
                'amount' => (float)$amountToPay * 100, 
                'currency' => 'usd', 
                'customer' => $customer->id,
            ]);

            $this->chargeID = $charge->id;
            return true;

        } catch (\Exception $ex) {
            return $this->error = $ex->getMessage();
        }
    }
}
