<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Parcel\CreateRequest;
use App\Http\Resources\PublicApi\OrderResource;
use App\Models\ApiLog;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ParcelController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRequest $request)
    {
        DB::beginTransaction();

        try {

            $order = Order::create([
                'shipping_service_id' => optional($request->parcel)['service_id'],
                'user_id' => Auth::id(),
                "merchant" => optional($request->parcel)['merchant'],
                "carrier" => optional($request->parcel)['carrier'],
                "tracking_id" => optional($request->parcel)['tracking_id'],
                "customer_reference" => optional($request->parcel)['customer_reference'],
                "measurement_unit" => optional($request->parcel)['measurement_unit'],
                "weight" => optional($request->parcel)['weight'],
                "length" => optional($request->parcel)['length'],
                "width" => optional($request->parcel)['width'],
                "height" => optional($request->parcel)['height'],
                "is_invoice_created" => true,
                "order_date" => now(),
                "is_shipment_added" => true,
                'status' => Order::STATUS_ORDER,
                'user_declared_freight' => optional($request->parcel)['shipment_value']??0,

                "sender_first_name" => optional($request->sender)['sender_first_name'],
                "sender_last_name" => optional($request->sender)['sender_last_name'],
                "sender_email" => optional($request->sender)['sender_email'],
                "sender_taxId" => optional($request->sender)['sender_taxId'],
            ]);

            $order->recipient()->create([
                "first_name" => optional($request->recipient)['first_name'],
                "last_name" => optional($request->recipient)['last_name'],
                "email" => optional($request->recipient)['email'],
                "phone" => optional($request->recipient)['phone'],
                "city" => optional($request->recipient)['city'],
                "street_no" => optional($request->recipient)['street_no'],
                "address" => optional($request->recipient)['address'],
                "address2" => optional($request->recipient)['address2'],
                "account_type" => optional($request->recipient)['account_type'],
                "tax_id" => optional($request->recipient)['tax_id'],
                "zipcode" => optional($request->recipient)['zipcode'],
                "state_id" => optional($request->recipient)['state_id'],
                "country_id" => optional($request->recipient)['country_id']
            ]);

            $isBattery = false;
            $isPerfume = false;
            foreach ($request->get('products',[]) as $product) {
                if(optional($product)['is_battery']){
                    $isBattery = true;
                }
                if(optional($product)['is_perfume']){
                    $isPerfume = true;
                }
                $order->items()->create([
                    "sh_code" => optional($product)['sh_code'],
                    "description" => optional($product)['description'],
                    "quantity" => optional($product)['quantity'],
                    "value" => optional($product)['value'],
                    "contains_battery" => optional($product)['is_battery'],
                    "contains_perfume" => optional($product)['is_perfume'],
                    "contains_flammable_liquid" => optional($product)['is_flameable'],
                ]);
            }
            if( $isBattery === true && $isPerfume === true){
                // DB::rollback();
                // return apiResponse(false,"please don't use battery and perfume in one parcel");
                throw new \Exception("please don't use battery and perfume in one parcels",500);
            }

            $orderValue = collect($request->get('products',[]))->sum(function($item){
                return $item['value'] * $item['quantity'];
            });

            $order->update([
                'warehouse_number' => "TEMPWHR-{$order->id}",
                "order_value" => $orderValue,
                'shipping_service_name' => $order->shippingService->name
            ]);

            $order->doCalculations();

            if ( getBalance() >= $order->gross_total ){
                $order->update([
                    'is_paid' => true,
                    'status' => Order::STATUS_PAYMENT_DONE
                ]);

                chargeAmount($order->gross_total,$order);
            }

            DB::commit();
            return apiResponse(true,"Parcel Created", OrderResource::make($order) );

        } catch (\Exception $ex) {
            DB::rollback();
           return apiResponse(false,$ex->getMessage());
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }
}
