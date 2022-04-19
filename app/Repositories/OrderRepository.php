<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderRepository
{
    protected $error;
    protected $chargeID;

    public function get(Request $request,$paginate = true,$pageSize=50,$orderBy = 'id',$orderType='asc')
    {
        $query = Order::query()
            ->where('status','>=',Order::STATUS_ORDER)
            ->has('user')
            ->with([
                'paymentInvoices',
                'user',
                'subOrders',
                'parentOrder'
            ]);
        if (Auth::user()->isUser()) {
            $query->where('user_id', Auth::id());
        }

        if($request->userType == 'domestic')
        {
            $query->where('sender_country_id', Country::US);
        }

        if ($request->userType == 'pickups') {
            $query->where('api_pickup_response' , '!=', null);
        }
        
        if($request->userType){
            $query->whereHas('user', function ($queryUser) use($request) {
                $queryUser->whereHas('role', function ($queryRole) use($request) {
                    return $queryRole->where('name', $request->userType);
                });
            });
        }

        if($request->order_date){
            $query->where('order_date', 'LIKE', "%{$request->date}%");
        }  
        if($request->name){
            $query->whereHas('user', function ($query) use($request) {
                return $query->where('name', 'LIKE', "%{$request->name}%");
            });
        }
        if($request->pobox_number){
            $query->whereHas('user', function ($query) use($request) {
                return $query->where('pobox_number', 'LIKE', "%{$request->pobox}%");
            });
        }
        if($request->warehouse_number){
            $query->where('warehouse_number', 'LIKE', "%{$request->warehouse_number}%");
        }
        if($request->merchant){
            $query->where('merchant', 'LIKE', "%{$request->merchant}%");
        }
        if($request->carrier){
            $query->where('carrier', 'LIKE', "%{$request->carrier}%");
        }
        if($request->gross_total){
            $query->where('gross_total', 'LIKE', "%{$request->amount}%");
        }
        if($request->tracking_id){
            $query->where('tracking_id', 'LIKE', "%{$request->tracking_id}%");
        }
        if($request->customer_reference){
            $query->where('customer_reference', 'LIKE', "%{$request->customer_reference}%");
        }
        if($request->corrios_tracking_code){
            $query->where('corrios_tracking_code', 'LIKE', "%{$request->tracking_code}%");
        }
        if($request->status){
            $query->where('status',$request->status);
        }
        if($request->orderType){
            if ($request->orderType === 'consolidated') {
                $query->where('is_consolidated',true);
            }

            if ($request->orderType === 'non-consolidated') {
                $query->where('is_consolidated',false);
            }
        }
        if($request->paymentStatus){
            if ($request->paymentStatus === 'paid') {
                $query->where('is_paid',true);
            }

            if ($request->paymentStatus === 'unpaid') {
                $query->where('is_paid',false);
            }
        }
        $query->orderBy($orderBy,$orderType);

        return $paginate ? $query->paginate($pageSize) : $query->get();
    }

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
            
            $order->items()->delete();

            foreach ($request->get('items',[]) as $item) {

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

            if ($order->isPaid() && $order->getPaymentInvoice()) 
            {
                $orderInvoice = $order->getPaymentInvoice();
               
                $orderInvoice->update([
                    'total_amount' => $orderInvoice->orders()->sum('gross_total'),
                ]);

                if ($orderInvoice->total_amount > $orderInvoice->paid_amount) {
                    
                    $orderInvoice->update([
                        'is_paid' => 0,
                    ]);
                    
                    $order->update([
                        'status' => Order::STATUS_PAYMENT_PENDING,
                        'is_paid' => 0,
                    ]);
                }
            }

            DB::commit();

            return true;
        } catch (\Exception $ex) {
            DB::rollback();
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

    public function setVolumetricDiscount($order)
    {
        $totalDiscountPercentage = 0;
        $volumetricDiscount = setting('volumetric_discount', null, $order->user->id);
        $discountPercentage = setting('discount_percentage', null, $order->user->id);

        if (!$volumetricDiscount || !$discountPercentage || $discountPercentage < 0) {
            return false;
        }

        $volumetricWeight = round($order->getWeight() > $order->weight ? $order->getWeight() : $order->weight, 2);
        
        $totalDiscountPercentage = ($discountPercentage) ? $discountPercentage/100 : 0;

        if ($volumetricWeight > $order->weight) {
            
            $volumeWeightBeforeDiscount = $volumetricWeight;
            
            $volumetricWeight = round($volumetricWeight - ($volumetricWeight * $totalDiscountPercentage), 2);
            $totalDiscountedWeight = $volumeWeightBeforeDiscount - $volumetricWeight;

            $order->update([
                'weight_discount' => $totalDiscountedWeight,
            ]);
        }

        return true;
    }
    
}
