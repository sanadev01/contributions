<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Models\Order;
use App\Models\State;
use App\Models\ApiLog;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\OrderRepository;
use App\Services\Converters\UnitsConverter;
use App\Services\Calculators\WeightCalculator;
use App\Http\Requests\Api\Parcel\CreateRequest;
use App\Http\Requests\Api\Parcel\UpdateRequest;
use App\Http\Resources\PublicApi\OrderResource;
use App\Repositories\ApiShippingServiceRepository;

class ParcelController extends Controller
{

    protected $usShippingService;
    protected $orderRepository;

    public function __construct(ApiShippingServiceRepository $usShippingService, OrderRepository $orderRepository)
    {
        $this->usShippingService = $usShippingService;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRequest $request)
    {
        
        $weight = optional($request->parcel)['weight']??0;
        $length = optional($request->parcel)['length']??0;
        $width = optional($request->parcel)['width']??0;
        $height = optional($request->parcel)['height']??0;

        if ( optional($request->parcel)['measurement_unit'] == 'kg/cm' ){
            $volumetricWeight = WeightCalculator::getVolumnWeight($length,$width,$height,'cm');
            $volumeWeight = round($volumetricWeight > $weight ? $volumetricWeight : $weight,2);
            if($volumeWeight > 30){
                return apiResponse(false,"Your ". $volumeWeight ." kg/cm weight has exceeded the limit. Please check the weight and dimensions. Weight shouldn't be greater than 30 kg/cm");
            }
        }else{
            $volumetricWeight = WeightCalculator::getVolumnWeight($length,$width,$height,'in');;
            $volumeWeight = round($volumetricWeight > $weight ? $volumetricWeight : $weight,2);
            if($volumeWeight > 65.15){
                return apiResponse(false,"Your ". $volumeWeight ." lbs/in weight has exceeded the limit. Please check the weight and dimensions. Weight shouldn't be greater than 66.15 lbs/in");
            }
        }

        $countryID = optional($request->recipient)['country_id'];
        $stateID = optional($request->recipient)['state_id'];
        
        if (!is_numeric( optional($request->recipient)['country_id'])){
            
            $country = Country::where('code', optional($request->recipient)['country_id'])->orwhere('id', optional($request->recipient)['country_id'])->first();
            $countryID = $country->id;
        }
        if (!is_numeric( optional($request->recipient)['state_id'])){

            $state = State::where('country_id', $countryID )->where('code', optional($request->recipient)['state_id'])->orwhere('id', optional($request->recipient)['state_id'])->first();
            $stateID = $state->id;
        }

        if ($countryID == 250) {
           if(!$this->usShippingService->isAvalaible($request))
           {
                return apiResponse(false, 'Seleceted Shipping service is not available for your account');
           }
        }
        
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

            $this->orderRepository->setVolumetricDiscount($order);
           
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
                "state_id" => $stateID,
                "country_id" =>$countryID 
            ]);
            
            if($countryID == Order::CHILE){

                $order->update([
                    "sender_address" => optional($request->sender)['sender_address'],
                    "sender_city" => optional($request->sender)['sender_city'],
                ]);
                $order->recipient()->update([
                    "region" => optional($request->recipient)['region'],
                ]);
            }

            if ($countryID == Order::US) {
              
                $order->update([
                    "sender_country_id" => optional($request->sender)['sender_country_id'],
                    "sender_state_id" => optional($request->sender)['sender_state_id'],
                    "sender_zipcode" => optional($request->sender)['sender_zipcode'],
                    "sender_address" => optional($request->sender)['sender_address'],
                    "sender_city" => optional($request->sender)['sender_city'],
                ]);
            }
            
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
                throw new \Exception("Please don't use battery and perfume in one parcels",500);
            }

            $orderValue = collect($request->get('products',[]))->sum(function($item){
                return $item['value'] * $item['quantity'];
            });

            $order->update([
                'warehouse_number' => "TEMPWHR-{$order->id}",
                "order_value" => $orderValue,
                'shipping_service_name' => $order->shippingService->name
            ]);
            
            if ($countryID == Order::US) {
                if(!$this->usShippingService->getUSShippingServiceRate($order))
                {
                    DB::rollback();
                    return apiResponse(false, $this->usShippingService->getError());
                }
            }

            $order->doCalculations();

            // if ( getBalance() >= $order->gross_total ){
            //     $order->update([
            //         'is_paid' => true,
            //         'status' => Order::STATUS_PAYMENT_DONE
            //     ]);

            //     chargeAmount($order->gross_total,$order);
            // }

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
    public function update(UpdateRequest $request, Order $parcel)
    {
        if ($parcel->isPaid()) {
            return apiResponse(false,'order can not be updated once payment has been paid');
        }

        $weight = optional($request->parcel)['weight']??0;
        $length = optional($request->parcel)['length']??0;
        $width = optional($request->parcel)['width']??0;
        $height = optional($request->parcel)['height']??0;

        if ( optional($request->parcel)['measurement_unit'] == 'kg/cm' ){
            $volumetricWeight = WeightCalculator::getVolumnWeight($length,$width,$height,'cm');
            $volumeWeight = round($volumetricWeight > $weight ? $volumetricWeight : $weight,2);
            if($volumeWeight > 30){
                return apiResponse(false,"Your ". $volumeWeight ." kg/cm weight has exceeded the limit. Please check the weight and dimensions. Weight shouldn't be greater than 30 kg/cm");
            }
        }else{
            $volumetricWeight = WeightCalculator::getVolumnWeight($length,$width,$height,'in');;
            $volumeWeight = round($volumetricWeight > $weight ? $volumetricWeight : $weight,2);
            if($volumeWeight > 65.15){
                return apiResponse(false,"Your ". $volumeWeight ." lbs/in weight has exceeded the limit. Please check the weight and dimensions. Weight shouldn't be greater than 66.15 lbs/in");
            }
        }

        $countryID = optional($request->recipient)['country_id'];
        $stateID = optional($request->recipient)['state_id'];
        
        if (!is_numeric( optional($request->recipient)['state_id'])){

            $state = State::where('code', optional($request->recipient)['state_id'])->orwhere('id', optional($request->recipient)['state_id'])->first();
            $stateID = $state->id;
        }
        if (!is_numeric( optional($request->recipient)['country_id'])){

            $country = Country::where('code', optional($request->recipient)['country_id'])->orwhere('id', optional($request->recipient)['country_id'])->first();
            $countryID = $country->id;
        }

        DB::beginTransaction();

        try {
            $parcel->update([
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

            $this->orderRepository->setVolumetricDiscount($parcel);
            
            $parcel->recipient()->update([
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
                "state_id" => $stateID,
                "country_id" =>$countryID 
            ]);
            
            if($countryID == 46){

                $parcel->update([
                    "sender_address" => optional($request->sender)['sender_address'],
                    "sender_city" => optional($request->sender)['sender_city'],
                ]);
                $parcel->recipient()->update([
                    "region" => optional($request->recipient)['region'],
                ]);
            }
            $parcel->items()->delete();
            $isBattery = false;
            $isPerfume = false;
            foreach ($request->get('products',[]) as $product) {
                
                if(optional($product)['is_battery']){
                    $isBattery = true;
                }
                if(optional($product)['is_perfume']){
                    $isPerfume = true;
                }
                $parcel->items()->create([
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
                throw new \Exception("Please don't use battery and perfume in one parcels",500);
            }

            $orderValue = collect($request->get('products',[]))->sum(function($item){
                return $item['value'] * $item['quantity'];
            });

            $parcel->update([
                'warehouse_number' => "TEMPWHR-{$parcel->id}",
                "order_value" => $orderValue,
                'shipping_service_name' => $parcel->shippingService->name
            ]);

            $parcel->doCalculations();

            // if ( getBalance() >= $order->gross_total ){
            //     $order->update([
            //         'is_paid' => true,
            //         'status' => Order::STATUS_PAYMENT_DONE
            //     ]);

            //     chargeAmount($order->gross_total,$order);
            // }

            DB::commit();
            return apiResponse(true,"Parcel Updated", OrderResource::make($parcel) );

        } catch (\Exception $ex) {
            DB::rollback();
           return apiResponse(false,$ex->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $parcel,$soft = true)
    {
        if ( $soft ){
            
            // if ( $parcel->isConsolidated() ){
            //     $parcel->subOrders()->sync([]);
            // }
            optional($parcel->affiliateSale)->delete();
            $parcel->delete();
            return apiResponse(true,"Order deleted" );
        }

        DB::beginTransaction();

        try {
            $parcel->items()->delete();
            $parcel->subOrders()->sync([]);
            optional($parcel->purchaseInvoice)->delete();
            optional($parcel->affiliateSale)->delete();
            $parcel->recipient()->delete();
            foreach ($parcel->images as $image) {
                $image->delete();
            }
            $parcel->delete();
            DB::commit();

            return apiResponse(true,"Order deleted" );
        } catch (\Exception $ex) {
            DB::rollback();

            return apiResponse(false,"error: ".$ex->getMessage() );
        }
    }
}
