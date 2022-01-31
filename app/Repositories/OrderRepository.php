<?php

namespace App\Repositories;

use Stripe\Charge;
use Stripe\Stripe;
use Stripe\Customer;
use App\Models\Order;
use App\Models\State;
use App\Models\Country;
use App\Events\OrderPaid;
use App\Facades\USPSFacade;
use Illuminate\Http\Request;
use App\Mail\User\PaymentPaid;
use App\Models\PaymentInvoice;
use App\Models\HandlingService;
use App\Models\ShippingService;
use App\Models\BillingInformation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\UPS\UPSShippingService;
use App\Services\USPS\USPSShippingService;
use App\Services\FedEx\FedExShippingService;
use App\Services\PaymentServices\AuthorizeNetService;

class OrderRepository
{
    protected $error;
    protected $chargeID;
    public $shippingServiceError;

    public function getOrderByIds(array $ids)
    {
        $query = Order::query();

        if ( Auth::user()->isUser() ){
            $query->where('user_id',Auth::id());
        }

        return $query->whereIn('id',$ids)->get();
    }

    public function updateSenderAddress(Request $request, Order $order)
    {
        $order->update([
            'sender_first_name' => $request->first_name,
            'sender_last_name' => $request->last_name,
            'sender_email' => $request->email,
            'sender_phone' => $request->phone,
            'sender_taxId' => $request->taxt_id,
            'sender_address' => $request->sender_address,
            'sender_city' => $request->sender_city,
            'sender_country_id' => $request->sender_country_id,
            'sender_state_id' => $request->sender_state_id,
            'sender_zipcode' => $request->sender_zipcode,
        ]);

        return $order;
    }

    public function updateRecipientAddress(Request $request, Order $order)
    {
        $order->update([
            'recipient_address_id' => $request->address_id
        ]);

        if ( $request->has('save_address') && !$request->address_id){
            (new AddressRepository)->store($request);
        }

        if ( $request->has('save_address') && $request->address_id ){
            session()->flash('alert-danger',__('address.duplicate_error'));
        }

        $request->merge([
            'phone' => "+".cleanString($request->phone)
        ]);
        
        if ( $order->recipient ){

            $order->recipient()->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'city' => ($request->service == 'postal_service') ? $request->city : null,
                'commune_id' => ($request->service == 'courier_express') ? $request->commune_id : null,
                'street_no' => $request->street_no,
                'address' => $request->address,
                'address2' => $request->address2,
                'account_type' => $request->account_type,
                'tax_id' => cleanString($request->tax_id),
                'zipcode' => cleanString($request->zipcode),
                'state_id' => $request->state_id,
                'country_id' => $request->country_id,
                'region' => $request->region,
            ]);

            return $order->recipient;

        }

        $order->recipient()->create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'city' => ($request->service == 'postal_service') ? $request->city : null,
            'commune_id' => ($request->service == 'courier_express') ? $request->commune_id : null,
            'street_no' => $request->street_no,
            'address' => $request->address,
            'address2' => $request->address2,
            'account_type' => $request->account_type,
            'tax_id' => $request->tax_id,
            'zipcode' => $request->zipcode,
            'state_id' => $request->state_id,
            'country_id' => $request->country_id,
            'region' => $request->region,
        ]);

        $order->refresh();

        return $order->recipient;
    }

    public function updateHandelingServices(Request $request, Order $order)
    {
        $order->syncServices($request->get('services',[]));

        $order->doCalculations();
        return true;
    }

    public function updateShippingAndItems(Request $request, Order $order)
    {
        DB::beginTransaction();

        try {
            $lastOrderItemQuantity = $order->items()->sum('quantity');
            $order->items()->delete();
            $product = $order->products->first();
            $totalQuantity = 0;
            // dd($product);
            // $productQuantity = $product->quantity;
            foreach ($request->get('items',[]) as $item) {
                if($product && $product->quantity  >= $totalQuantity && $product->sh_code == $item['sh_code'] ){
                    $totalQuantity+=$item['quantity'];
                }
                
                $order->items()->create([
                    'sh_code' => optional($item)['sh_code'],
                    'description' => optional($item)['description'],
                    'quantity' => optional($item)['quantity'],
                    'value' => optional($item)['value'],
                    'contains_battery' => optional($item)['dangrous_item'] == 'contains_battery' ? true: false,
                    'contains_perfume' => optional($item)['dangrous_item'] == 'contains_perfume' ? true: false,
                    'contains_flammable_liquid' => optional($item)['dangrous_item'] == 'contains_flammable_liquid' ? true: false,
                ]);
            }

            if($product && $product->quantity + $lastOrderItemQuantity < $totalQuantity){
                $remainingQuantity = $product->quantity+1;
                DB::rollback();
                session()->flash('alert-danger','Your Quantity Is '. $remainingQuantity . ' You Cannot Add More Than '. $remainingQuantity );
                return false;
            }
            $totalDifference = $totalQuantity - $lastOrderItemQuantity;
            if($product){
                $product->update([
                    'quantity'=>$product->quantity - $totalDifference,
                ]);
            }

            $shippingService = ShippingService::find($request->shipping_service_id);

            if ($shippingService->service_sub_class == ShippingService::USPS_PRIORITY_INTERNATIONAL ||  $shippingService->service_sub_class == ShippingService::USPS_FIRSTCLASS_INTERNATIONAL) {
                if(!$this->getUSPSInternationServiceRates($request, $order, $shippingService->service_sub_class))
                {
                    DB::rollback();
                    session()->flash('alert-danger','orders.Error While placing Order '.$this->error);
                    return false;
                }
            }

            $order->update([
                'customer_reference' => $request->customer_reference,
                'shipping_service_id' => $shippingService->id,
                'shipping_service_name' => $shippingService->name,
                'tax_modality' => $request->tax_modality,
                'is_invoice_created' => true,
                'user_declared_freight' => $request->user_declared_freight,
                'comission' => 0,
                'insurance_value' => 0,
                'status' => $order->isPaid() ? ($order->status < Order::STATUS_ORDER ? Order::STATUS_ORDER : $order->status) : Order::STATUS_ORDER
            ]);
            
            $order->doCalculations();

            DB::commit();
            session()->flash('alert-success','orders.Sender Updated');
            return true;
        } catch (\Exception $ex) {
            DB::rollback();
            $this->error = $ex->getMessage();
             session()->flash('alert-danger','orders.Error While placing Order'." ".$this->error);
            return false;
        }
    }

    public function checkout(Request $request, PaymentInvoice $paymentInvoice)
    {
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
                    'card_no' => ($request->payment_gateway == 'stripe_ach') ? $request->account_no : $request->card_no,
                    'expiration' => ($request->payment_gateway == 'stripe_ach') ? null : $request->expiration,
                    'cvv' => ($request->payment_gateway == 'stripe_ach') ? $request->routing_number : $request->cvv,
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

            if($request->payment_gateway == 'stripe')
            {
                $transactionID = PaymentInvoice::generateUUID('DP-');
                $this->stripePayment($request, $paymentInvoice->total_amount, $paymentInvoice->uuid);

                if($this->error != null)
                {
                    DB::rollBack();
                    return false;
                }
            }

            if($request->payment_gateway == 'stripe_ach')
            {
                $transactionID = PaymentInvoice::generateUUID('DP-');
                $this->stripeAchPayment($request, $paymentInvoice->total_amount);

                if($this->error != null)
                {
                    DB::rollBack();
                    return false;
                }
            }

            if($request->payment_gateway == 'authorize')
            {
                $authorizeNetService = new AuthorizeNetService();

                $response = $authorizeNetService->makeCreditCardPayement($billingInformation,$paymentInvoice);

                if ( !$response->success ){
                    $this->error = json_encode($response->message);
                    DB::rollBack();
                    return false;
                }
            }
            

            $paymentInvoice->update([ 
                'last_four_digits' => substr($billingInformation->card_no,-4),
                'is_paid' => true
            ]);

            $paymentInvoice->transactions()->create([
                'transaction_id' => ($request->payment_gateway == 'stripe' || $request->payment_gateway == 'stripe_ach') ? $this->chargeID : $response->data->getTransId(),
                'amount' => $paymentInvoice->total_amount
            ]);

            $paymentInvoice->orders()->update([
                'is_paid' => true,
                'status' => Order::STATUS_PAYMENT_DONE
            ]);

            event(new OrderPaid($paymentInvoice->orders, true));
            
            try {
                \Mail::send(new PaymentPaid($paymentInvoice));
            } catch (\Exception $ex) {
                \Log::info('Payment Paid email send error: '.$ex->getMessage());
            }

            DB::commit();

            return true;

        } catch (\Exception $ex) {
            DB::rollBack();
            $this->error = $ex->getMessage();
            return false;
        }
        
    }
    
    

    public function getError()
    {
        return $this->error;
    }
    
    public function getOdersForExport($request)
    {
        $orders = Order::where('status','>=',Order::STATUS_ORDER)
        ->has('user');
        if (Auth::user()->isUser()) {
            $orders->where('user_id', Auth::id());
        }
        $startDate  = $request->start_date.' 00:00:00';
        $endDate    = $request->end_date.' 23:59:59';
        if ( $request->start_date ){
            $orders->where('order_date','>=',$startDate);
        }
        if ( $request->end_date ){
            $orders->where('order_date','<=',$endDate);
        }
        
        return $orders->orderBy('id')->get();
    }
    
    private function stripePayment($request, $total_amount, $InvoiceId)
    {
        $stripeSecret = setting('STRIPE_SECRET', null, null, true);
        
        Stripe::setApiKey($stripeSecret);
        try {
            $charge = Charge::create ([
                'amount' => (float)$total_amount * 100,
                'currency' => "usd",
                'source' => $request->stripe_token,
                'description' => auth()->user()->pobox_number.' '.'paid to HomeDelivery against payment invoice# '.$InvoiceId,
            ]);
            
            $this->chargeID = $charge->id;
            return true;

        } catch (\Exception $ex) {
            $this->error = $ex->getMessage();

            return false;
        }
    }

    private function stripeAchPayment($request, $total_amount)
    {
        $stripeSecret = setting('STRIPE_SECRET', null, null, true);
        
        Stripe::setApiKey($stripeSecret);

        try {

            $customer = Customer::create([
                'description' => $request->first_name . ' ' . $request->last_name,
                'source' => $request->stripe_token,
            ]);

            if($this->verifyCustomer($customer, $total_amount))
            {
                return true;
            }

        } catch (\Exception $th) {
            $this->error = $th->getMessage();

            return false;
        }
        
    }

    private function verifyCustomer($customer, $total_amount)
    {
        try {

            // get the existing bank account of customer
            $bank_account = Customer::retrieveSource(
                $customer->id,
                $customer->default_source
            );

            // verify the account(stripe default)
            $bank_account->verify(['amounts' => [32, 45]]);

            if($this->stripeAchCharge($customer, $total_amount))
            {
                return true;
            }

        } catch (\Exception $ex) {

            $this->error = $ex->getMessage();

            return false;
        }
        
    }

    private function stripeAchCharge($customer, $total_amount)
    {
        try {

            $stripeSecret = setting('STRIPE_SECRET', null, null, true);

            $stripe = new \Stripe\StripeClient($stripeSecret);

            $charge = $stripe->charges->create([
                'amount' => (float)$total_amount * 100, 
                'currency' => 'usd', 
                'customer' => $customer->id,
            ]);

            $this->chargeID = $charge->id;
            return true;

        } catch (\Exception $ex) {
            return $this->error = $ex->getMessage();
        }
    }

    public function getShippingServices($order)
    {
        $shippingServices = collect() ;

        if(optional($order->recipient)->country_id == Order::US)
        {
            $uspsShippingService = new USPSShippingService($order);
            $upsShippingService = new UPSShippingService($order);
            $fedExShippingService = new FedExShippingService($order);
            
            foreach (ShippingService::query()->active()->get() as $shippingService) 
            {
                if ($uspsShippingService->isAvailableFor($shippingService)) {
                    $shippingServices->push($shippingService);
                }

                if ($upsShippingService->isAvailableFor($shippingService)) {
                    $shippingServices->push($shippingService);
                }

                if ($fedExShippingService->isAvailableFor($shippingService)) {
                    $shippingServices->push($shippingService);
                }
            }
        } else
        {
            foreach (ShippingService::query()->has('rates')->active()->get() as $shippingService) 
            {
                if ($shippingService->isAvailableFor($order)) {
                    $shippingServices->push($shippingService);
                }elseif($shippingService->getCalculator($order)->getErrors() != null && $shippingServices->isEmpty()){
                    $this->shippingServiceError = 'Shipping Service not Available Error: {'.$shippingService->getCalculator($order)->getErrors().'}';
                }
            }

            // USPS Intenrational Services
            if ($order->sender_country_id == Order::US && optional($order->recipient)->country_id != Order::US && setting('usps', null, $order->user->id)) 
            {
                $uspsShippingService = new USPSShippingService($order);

                foreach (ShippingService::query()->active()->get() as $shippingService)
                {
                    if ($uspsShippingService->isAvailableForInternational($shippingService)) {
                        $shippingServices->push($shippingService);
                    }
                }
            }

            if ($shippingServices->isEmpty() && $this->shippingServiceError == null) {
                $this->shippingServiceError = ($order->recipient->commune_id != null) ? 'Shipping Service not Available for the Region you have selected' : 'Shipping Service not Available for the Country you have selected';
            }
        }

        if ($shippingServices->isNotEmpty()) {
           $shippingServices = $this->filterShippingServices($shippingServices, $order);
        }

        return $shippingServices;
    }

    public function getShippingServicesError()
    {
        return $this->shippingServiceError;
    }

    private function filterShippingServices($shippingServices, $order)
    {
        if($shippingServices->contains('service_sub_class', ShippingService::USPS_PRIORITY) 
            || $shippingServices->contains('service_sub_class', ShippingService::USPS_FIRSTCLASS)
            || $shippingServices->contains('service_sub_class', ShippingService::USPS_PRIORITY_INTERNATIONAL)
            || $shippingServices->contains('service_sub_class', ShippingService::USPS_FIRSTCLASS_INTERNATIONAL)
            || $shippingServices->contains('service_sub_class', ShippingService::UPS_GROUND))
        {
            if(!setting('usps', null, $order->user->id))
            {
                $this->shippingServiceError = 'USPS is not enabled for this user';
                $shippingServices = $shippingServices->filter(function ($shippingService, $key) {
                    return $shippingService->service_sub_class != ShippingService::USPS_PRIORITY 
                        && $shippingService->service_sub_class != ShippingService::USPS_FIRSTCLASS
                        && $shippingService->service_sub_class != ShippingService::USPS_PRIORITY_INTERNATIONAL
                        && $shippingService->service_sub_class != ShippingService::USPS_FIRSTCLASS_INTERNATIONAL;
                });
            }
            if(!setting('ups', null, $order->user->id))
            {
                $this->shippingServiceError = 'UPS is not enabled for this user';
                $shippingServices = $shippingServices->filter(function ($shippingService, $key) {
                    return $shippingService->service_sub_class != ShippingService::UPS_GROUND;
                });
            }

            if($shippingServices->isNotEmpty()){
                $this->shippingServiceError = null;
            }
        }

        if($order->recipient->country_id == Order::BRAZIL)
        {
            // If sinerlog is enabled for the user, then remove the Correios services
            if(setting('sinerlog', null, $order->user->id))
            {
                $shippingServices = $shippingServices->filter(function ($item, $key)  {
                    return $item->service_sub_class != '33162' && $item->service_sub_class != '33170' && $item->service_sub_class != '33197';
                });
            }

            // If sinerlog is not enabled for the user then remove Sinerlog services from shipping service
            if(!setting('sinerlog', null, $order->user->id))
            {
                $shippingServices = $shippingServices->filter(function ($item, $key)  {
                    return $item->service_sub_class != '33163' && $item->service_sub_class != '33171' && $item->service_sub_class != '33198';
                });
            }
            
            if($shippingServices->isEmpty()){
                $this->shippingServiceError = 'Please check your parcel dimensions';
            }
        }

        return $shippingServices;
    }

    private function getUSPSInternationServiceRates($request, $order, $service)
    {
        $response  = USPSFacade::getRecipientRates($order, $service);
        if ($response->success == true) {

            $request->merge([
                'user_declared_freight' => $response->data['total_amount'],
            ]);

            return true;
        }
        $this->error = $response->message;
        return false;
    }

}
