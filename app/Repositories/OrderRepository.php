<?php

namespace App\Repositories;

use App\Models\BillingInformation;
use App\Models\Country;
use App\Models\HandlingService;
use App\Models\Order;
use App\Models\PaymentInvoice;
use App\Models\ShippingService;
use App\Models\State;
use App\Services\PaymentServices\AuthorizeNetService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderRepository extends Model
{
    protected $error;

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
                'tax_id' => $request->tax_id,
                'zipcode' => $request->zipcode,
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


        return $order->recipient;
    }

    public function updateHandelingServices(Request $request, Order $order)
    {
        $order->services()->delete();

        foreach($request->get('services',[]) as $serviceId){
            $service = HandlingService::find($serviceId);

            if (!$service ) continue;

            $order->services()->create([
                'service_id' => $service->id,
                'name' => $service->name,
                'cost' => $service->cost,
                'price' => $service->price,
            ]);
        }

        return true;
    }

    public function updateShippingAndItems(Request $request, Order $order)
    {
        DB::beginTransaction();

        try {
            
            $order->items()->delete();

            $orderValue =0;

            $battriesCount = 0;
            $perfumeCount = 0;
            $flameableCount = 0 ;
            foreach ($request->get('items',[]) as $item) {
                $orderValue += (optional($item)['quantity'] * optional($item)['value']);

                $battriesCount += optional($item)['dangrous_item'] == 'contains_battery' ? 1: 0;
                $perfumeCount += optional($item)['dangrous_item'] == 'contains_perfume' ? 1: 0;
                $flameableCount += optional($item)['dangrous_item'] == 'contains_flammable_liquid' ? 1: 0;

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

            $shippingService = ShippingService::find($request->shipping_service_id);

            $shippingCost = $shippingService->getRateFor($order);
            $additionalServicesCost = $order->services()->sum('price');
            $commission = 0; // not implemented yet
            $insurance = 0; // not implemented yet

            $battriesExtra = $shippingService->contains_battery_charges * $battriesCount;
            $pefumeExtra = $shippingService->contains_perfume_charges * $perfumeCount;
            $flameableExtra = $shippingService->contains_flammable_liquid_charges * $flameableCount;

            $dangrousGoodsCost = $battriesExtra + $pefumeExtra + $flameableExtra ;

            $total = $shippingCost + $additionalServicesCost + $commission + $insurance + $dangrousGoodsCost;
            
            $discount = 0; // not implemented yet
            $gross_total = $total - $discount;
            

            $order->update([
                'customer_reference' => $request->customer_reference,
                'shipping_service_id' => $shippingService->id,
                'shipping_service_name' => $shippingService->name,
                'tax_modality' => $request->tax_modality,
                'is_invoice_created' => true,
                
                // figures
                'order_value' => $orderValue,
                'shipping_value' => $shippingCost,
                'comission' => $commission,
                'dangrous_goods' => $dangrousGoodsCost,
                'total' => $total,
                'discount' => $discount,
                'gross_total' => $gross_total,
                'insurance_value' => $insurance,
                'status' => Order::STATUS_ORDER
            ]);

            DB::commit();

            return true;
        } catch (\Exception $ex) {
            DB::rollback();
            return false;
        }
    }



    public function createConsolidationRequest(Request $request, Order $order)
    {

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

}
