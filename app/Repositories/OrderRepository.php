<?php

namespace App\Repositories;

use App\Events\OrderPaid;
use App\Mail\User\PaymentPaid;
use App\Models\BillingInformation;
use App\Models\Country;
use App\Models\HandlingService;
use App\Models\Order;
use App\Models\PaymentInvoice;
use App\Models\ShippingService;
use App\Models\State;
use App\Services\PaymentServices\AuthorizeNetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderRepository
{
    protected $error;

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
                'city' => $request->city,
                'street_no' => $request->street_no,
                'address' => $request->address,
                'address2' => $request->address2,
                'account_type' => $request->account_type,
                'tax_id' => cleanString($request->tax_id),
                'zipcode' => cleanString($request->zipcode),
                'state_id' => $request->state_id,
                'country_id' => $request->country_id,
            ]);

            return $order->recipient;

        }

        $order->recipient()->create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'city' => $request->city,
            'street_no' => $request->street_no,
            'address' => $request->address,
            'address2' => $request->address2,
            'account_type' => $request->account_type,
            'tax_id' => $request->tax_id,
            'zipcode' => $request->zipcode,
            'state_id' => $request->state_id,
            'country_id' => $request->country_id,
        ]);

        $order->refresh();

        return $order->recipient;
    }

    public function updateHandelingServices(Request $request, Order $order)
    {
        $order->syncServices($request->get('services',[]));
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
            $productQuantity = $product->quantity;
            foreach ($request->get('items',[]) as $item) {
                if($product->quantity  >= $totalQuantity && $product->sh_code == $item['sh_code'] ){
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

            if($product->quantity + $lastOrderItemQuantity < $totalQuantity){
                session()->flash('alert-danger','Your Quantity Is '. $product->quantity . ' You Cannot Add More Than '. $product->quantity );
                DB::rollback();
                return false;
            }
            $totalDifference = $totalQuantity - $lastOrderItemQuantity;
            $product->update([
                'quantity'=>$product->quantity - $totalDifference,
            ]);
            $shippingService = ShippingService::find($request->shipping_service_id);

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
            session()->flash('alert-success','orders.Sender Update Error');
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

            $authorizeNetService = new AuthorizeNetService();

            $response = $authorizeNetService->makeCreditCardPayement($billingInformation,$paymentInvoice);


            if ( !$response->success ){
                $this->error = json_encode($response->message);
                DB::rollBack();
                return false;
            }

            $paymentInvoice->update([ 
                'last_four_digits' => substr($billingInformation->card_no,-4),
                'is_paid' => true
            ]);

            $paymentInvoice->transactions()->create([
                'transaction_id' => $response->data->getTransId(),
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
        
        if ( $request->start_date ){
            $orders->where('order_date','>',$request->start_date);
        }
        
        if ( $request->end_date ){
            $orders->where('order_date','<=',$request->end_date);
        }
        
        return $orders->get();
    }



}
