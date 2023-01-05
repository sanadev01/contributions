<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Models\Order;
use App\Models\State;
use App\Models\ApiLog;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Models\ShippingService;
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
    protected $apiShippingService;
    protected $orderRepository;

    public function __construct(ApiShippingServiceRepository $apiShippingService, OrderRepository $orderRepository)
    {
        $this->apiShippingService = $apiShippingService;
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
        \Log::info('request Data');
        \Log::info($request);
        $weight = optional($request->parcel)['weight']??0;
        $length = optional($request->parcel)['length']??0;
        $width = optional($request->parcel)['width']??0;
        $height = optional($request->parcel)['height']??0;

        $shippingService = ShippingService::find($request->parcel['service_id'] ?? null);

        if (!$shippingService) {
            return apiResponse(false,'Shipping service not found.');
        }

        if (!$shippingService->active) {
            return apiResponse(false,'Selected shipping service is currently not available.');
        }

        if (!setting('anjun_api', null, \App\Models\User::ROLE_ADMIN) && $shippingService->isAnjunService()) {
            return apiResponse(false,$shippingService->name.' is currently not available.');
        }

        if (setting('anjun_api', null, \App\Models\User::ROLE_ADMIN)) {
            if ($shippingService->service_sub_class == ShippingService::Packet_Mini) {
                return apiResponse(false,$shippingService->name.' is currently not available.');
            }

            if ($shippingService->service_sub_class == ShippingService::Packet_Standard) {
                $shippingService = ShippingService::where('service_sub_class', ShippingService::AJ_Packet_Standard)->first();
            }

            if ($shippingService->service_sub_class == ShippingService::Packet_Express) {
                $shippingService = ShippingService::where('service_sub_class', ShippingService::AJ_Packet_Express)->first();
            }
        }

        if ( optional($request->parcel)['measurement_unit'] == 'kg/cm' ){
            $volumetricWeight = WeightCalculator::getVolumnWeight($length,$width,$height,'cm');
            $volumeWeight = round($volumetricWeight > $weight ? $volumetricWeight : $weight,2);
            
            if($shippingService->isCorreiosService() && $volumeWeight > 30){
                return apiResponse(false,"Your ". $volumeWeight ." kg/cm weight has exceeded the limit. Please check the weight and dimensions. Weight shouldn't be greater than 30 kg/cm");
            }

        }else{
            $volumetricWeight = WeightCalculator::getVolumnWeight($length,$width,$height,'in');;
            $volumeWeight = round($volumetricWeight > $weight ? $volumetricWeight : $weight,2);
            
            if($shippingService->isCorreiosService() && $volumeWeight > 65.15){
                return apiResponse(false,"Your ". $volumeWeight ." lbs/in weight has exceeded the limit. Please check the weight and dimensions. Weight shouldn't be greater than 66.15 lbs/in");
            }
        }

        $senderCountryID = $request->sender['sender_country_id'] ?? null;
        $senderStateID = $request->sender['sender_state_id'] ?? null;
        
        $recipientCountryId = optional($request->recipient)['country_id'];
        $stateID = optional($request->recipient)['state_id'];
        
        if ($senderCountryID && !is_numeric($senderCountryID)) {
            $senderCountryID = Country::where('code', $senderCountryID)->orWhere('name', $senderCountryID)->first()->id;
        }

        if ($senderStateID && !is_numeric($senderStateID)) {
            $senderStateID = State::where([['code', $senderStateID],['country_id', $senderCountryID]])->orWhere([['name', $senderStateID], ['country_id', $senderCountryID]])->first()->id;
        }
        
        if (!is_numeric( optional($request->recipient)['country_id'])){
            
            $country = Country::where('code', optional($request->recipient)['country_id'])->orwhere('id', optional($request->recipient)['country_id'])->first();
            $recipientCountryId = $country->id;
        }
        if (!is_numeric( optional($request->recipient)['state_id'])){

            $state = State::where('country_id', $recipientCountryId )->where('code', optional($request->recipient)['state_id'])->orwhere('id', optional($request->recipient)['state_id'])->first();
            $stateID = $state->id;
        }

        if($shippingService->isDomesticService() && !$this->apiShippingService->isAvalaible($shippingService, $volumeWeight))
        {
            return apiResponse(false, $this->apiShippingService->getError());
        }

        if ($shippingService->isDomesticService() && $recipientCountryId != Country::US) {
            return apiResponse(false, 'this service is availaible for US address only');
        }

        if($shippingService->isInternationalService() && !$this->apiShippingService->isAvailableForInternational($shippingService, $volumeWeight)){
            return apiResponse(false, $this->apiShippingService->getError());
        }

        if ($shippingService->isInternationalService() && $recipientCountryId == Country::US) {
            return apiResponse(false, 'this service is not availaible for US address');
        }

        if ($shippingService->isColombiaService()) {
            if ($recipientCountryId != Country::COLOMBIA) {
                return apiResponse(false, 'this service is availaible for Colombia address only');          
            }

            if (!$this->apiShippingService->isAvalaible($shippingService, $volumeWeight)) {
                return apiResponse(false, $this->apiShippingService->getError());
            }
        }
        
        DB::beginTransaction();

        try {

            $order = Order::create([
                'shipping_service_id' => $shippingService->id,
                'user_id' => Auth::id(),
                "merchant" => optional($request->parcel)['merchant'],
                "carrier" => optional($request->parcel)['carrier'],
                "tracking_id" => optional($request->parcel)['tracking_id'],
                "customer_reference" => optional($request->parcel)['customer_reference'],
                "measurement_unit" => optional($request->parcel)['measurement_unit'],
                "weight" =>  round(optional($request->parcel)['weight'],2),
                "length" =>  round(optional($request->parcel)['length'],2),
                "width" =>   round(optional($request->parcel)['width'],2),
                "height" =>  round(optional($request->parcel)['height'],2),
                "is_invoice_created" => true,
                "order_date" => now(),
                "is_shipment_added" => true,
                'status' => Order::STATUS_ORDER,
                'user_declared_freight' => optional($request->parcel)['shipment_value']??0,

                "sender_first_name" => optional($request->sender)['sender_first_name'],
                "sender_last_name" => optional($request->sender)['sender_last_name'],
                "sender_email" => optional($request->sender)['sender_email'],
                "sender_taxId" => optional($request->sender)['sender_taxId'],
                'sender_country_id' => $senderCountryID,
                'sender_state_id' => $senderStateID,
                'sender_city' => optional($request->sender)['sender_city'],
                'sender_address' => optional($request->sender)['sender_address'],
                'sender_phone' => optional($request->sender)['sender_phone'],
                'sender_zipcode' => optional($request->sender)['sender_zipcode'],
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
                "country_id" =>$recipientCountryId
            ]);
            
            if($recipientCountryId == Order::CHILE || $recipientCountryId == Order::COLOMBIA){
                $order->recipient()->update([
                    "region" => optional($request->recipient)['region'],
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

            if($recipientCountryId == Order::US && !$order->shippingService->isDomesticService()){
                DB::rollback();

                return apiResponse(false, 'this service can not be use against US address');
            }
            
            if ($order->shippingService->isDomesticService() || $order->shippingService->isInternationalService()) {
                if(!$this->apiShippingService->getUSShippingServiceRate($order))
                {
                    DB::rollback();
                    return apiResponse(false, $this->apiShippingService->getError());
                }
            }

            $order->doCalculations();

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
    public function show(Order $parcel)
    {
        return apiResponse(true,"Get Parcel Data successfully", OrderResource::make($parcel) );
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

        $shippingService = ShippingService::find($request->parcel['service_id'] ?? null);

        if (!$shippingService) {
            return apiResponse(false,'Shipping service not found.');
        }

        if (!setting('anjun_api', null, \App\Models\User::ROLE_ADMIN) && $shippingService->isAnjunService()) {
            return apiResponse(false,$shippingService->name.' is currently not available.');
        }

        if (setting('anjun_api', null, \App\Models\User::ROLE_ADMIN)) {
            if ($shippingService->service_sub_class == ShippingService::Packet_Mini) {
                return apiResponse(false,$shippingService->name.' is currently not available.');
            }

            if ($shippingService->service_sub_class == ShippingService::Packet_Standard) {
                $shippingService = ShippingService::where('service_sub_class', ShippingService::AJ_Packet_Standard)->first();
            }

            if ($shippingService->service_sub_class == ShippingService::AJ_Packet_Express) {
                $shippingService = ShippingService::where('service_sub_class', ShippingService::AJ_Packet_Express)->first();
            }
        }

        if ( optional($request->parcel)['measurement_unit'] == 'kg/cm' ){
            $volumetricWeight = WeightCalculator::getVolumnWeight($length,$width,$height,'cm');
            $volumeWeight = round($volumetricWeight > $weight ? $volumetricWeight : $weight,2);
            
            if($shippingService->isCorreiosService() && $volumeWeight > 30){
                return apiResponse(false,"Your ". $volumeWeight ." kg/cm weight has exceeded the limit. Please check the weight and dimensions. Weight shouldn't be greater than 30 kg/cm");
            }

        }else{
            $volumetricWeight = WeightCalculator::getVolumnWeight($length,$width,$height,'in');;
            $volumeWeight = round($volumetricWeight > $weight ? $volumetricWeight : $weight,2);
            
            if($shippingService->isCorreiosService() && $volumeWeight > 65.15){
                return apiResponse(false,"Your ". $volumeWeight ." lbs/in weight has exceeded the limit. Please check the weight and dimensions. Weight shouldn't be greater than 66.15 lbs/in");
            }
        }

        $senderCountryID = $request->sender['sender_country_id'] ?? null;
        $senderStateID = $request->sender['sender_state_id'] ?? null;

        $recipientCountryId = optional($request->recipient)['country_id'];
        $stateID = optional($request->recipient)['state_id'];

        if ($senderCountryID && !is_numeric($senderCountryID)) {
            $senderCountryID = Country::where('code', $senderCountryID)->orWhere('name', $senderCountryID)->first()->id;
        }

        if ($senderStateID && !is_numeric($senderStateID)) {
            $senderStateID = State::where([['code', $senderStateID],['country_id', $senderCountryID]])->orWhere([['name', $senderStateID], ['country_id', $senderCountryID]])->first()->id;
        }
        
        if (!is_numeric( optional($request->recipient)['state_id'])){

            $state = State::where('code', optional($request->recipient)['state_id'])->orwhere('id', optional($request->recipient)['state_id'])->first();
            $stateID = $state->id;
        }
        if (!is_numeric( optional($request->recipient)['country_id'])){

            $country = Country::where('code', optional($request->recipient)['country_id'])->orwhere('id', optional($request->recipient)['country_id'])->first();
            $recipientCountryId = $country->id;
        }

       
        if($shippingService->isDomesticService() && !$this->apiShippingService->isAvalaible($shippingService, $volumeWeight))
        {
            return apiResponse(false, $this->apiShippingService->getError());

            if ($recipientCountryId != Country::US) {
                return apiResponse(false, 'this service is availaible for US address only');
            }
        }

        if($shippingService->isInternationalService() && !$this->apiShippingService->isAvailableForInternational($shippingService, $volumeWeight)){
            return apiResponse(false, $this->apiShippingService->getError());

            if ($recipientCountryId == Country::US) {
                return apiResponse(false, 'this service is not availaible for US address');
            }
        }

        if ($shippingService->isColombiaService()) {
            if ($recipientCountryId != Country::COLOMBIA) {
                return apiResponse(false, 'this service is availaible for Colombia address only');          
            }

            if (!$this->apiShippingService->isAvalaible($shippingService, $volumeWeight)) {
                return apiResponse(false, $this->apiShippingService->getError());
            }
        }
        

        DB::beginTransaction();

        try {
            $parcel->update([
                'shipping_service_id' => $shippingService->id,
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
                'sender_country_id' => $senderCountryID,
                'sender_state_id' => $senderStateID,
                'sender_city' => optional($request->sender)['sender_city'],
                'sender_address' => optional($request->sender)['sender_address'],
                'sender_phone' => optional($request->sender)['sender_phone'],
                'sender_zipcode' => optional($request->sender)['sender_zipcode'],
            ]);

            //CHECK VOL WEIGHT OF PARCEL AND SET DISCOUNT
            $totalDiscountPercentage = 0;
            $volumetricDiscount = setting('volumetric_discount', null, $parcel->user->id);
            $discountPercentage = setting('discount_percentage', null, $parcel->user->id);
            
            if (!$volumetricDiscount || !$discountPercentage || $discountPercentage < 0 || $discountPercentage == 0) {
                return false;
            }
            if ( optional($request->parcel)['measurement_unit'] == 'kg/cm' ){
                $volumetricWeight = WeightCalculator::getVolumnWeight(optional($request->parcel)['length'],optional($request->parcel)['width'],optional($request->parcel)['height'],'cm');
            }else {
                $volumetricWeight = WeightCalculator::getVolumnWeight(optional($request->parcel)['length'],optional($request->parcel)['width'],optional($request->parcel)['height'],'in');
            }
            $volumeWeight = round($volumetricWeight > optional($request->parcel)['weight'] ? $volumetricWeight : optional($request->parcel)['weight'],2);
            $totalDiscountPercentage = ($discountPercentage) ? $discountPercentage/100 : 0;
            if ($volumeWeight > optional($request->parcel)['weight']) {

                $consideredWeight = $volumeWeight - optional($request->parcel)['weight'];
                $volumeWeight = round($consideredWeight - ($consideredWeight * $totalDiscountPercentage), 2);
                $totalDiscountedWeight = $consideredWeight - $volumeWeight;
                $parcel->update([
                    "weight_discount" => $totalDiscountedWeight,
                ]);
            }else {
                $parcel->update([
                    "weight_discount" => null,
                ]);
            }
            
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
                "country_id" =>$recipientCountryId 
            ]);
            
            if($recipientCountryId ==  Country::Chile){

                $parcel->update([
                    "sender_address" => optional($request->sender)['sender_address'],
                    "sender_city" => optional($request->sender)['sender_city'],
                ]);
            }

            if ($recipientCountryId == Country::Chile || $recipientCountryId == Country::COLOMBIA) {
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

            if ($shippingService->isDomesticService() || $shippingService->isInternationalService()) {
                if(!$this->apiShippingService->getUSShippingServiceRate($parcel))
                {
                    DB::rollback();
                    return apiResponse(false, $this->apiShippingService->getError());
                }
            }

            $parcel->doCalculations();

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
        if ( $soft && $parcel->status < Order::STATUS_PAYMENT_DONE ){
            
            optional($parcel->affiliateSale)->delete();
            $parcel->delete();
            return apiResponse(true,"Order deleted" );
        }else{
            return apiResponse(false,"Order can't deleted your order proceed for shipping" );
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

            return apiResponse(true,"Orders deleted" );
        } catch (\Exception $ex) {
            DB::rollback();

            return apiResponse(false,"error: ".$ex->getMessage() );
        }
    }

    public function updateItems(Request $request, Order $parcel)
    {
        DB::beginTransaction();

        try {
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
                "order_value" => $orderValue,
            ]);

            DB::commit();
            return apiResponse(true,"Parcel Items Updated", OrderResource::make($parcel) );

        } catch (\Exception $ex) {
            DB::rollback();
           return apiResponse(false,$ex->getMessage());
        }
    }

}
