<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Models\Order;
use App\Models\State;
use App\Models\ApiLog;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Models\ProfitSetting;
use App\Models\ShippingService;
use FlyingLuscas\Correios\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\Validator;
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
        Log::info('request Data');
        Log::info($request);

        $weight = optional($request->parcel)['weight'] ?? 0;
        $length = optional($request->parcel)['length'] ?? 0;
        $width = optional($request->parcel)['width'] ?? 0;
        $height = optional($request->parcel)['height'] ?? 0;

        $shippingService = ShippingService::find($request->parcel['service_id'] ?? null);

        if (!$shippingService) {
            return apiResponse(false, 'Shipping service not found.');
        }

        if (!$shippingService->active) {
            return apiResponse(false, 'Selected shipping service is currently not available.');
        }

        // if (!setting('anjun_api', null, \App\Models\User::ROLE_ADMIN) && $shippingService->isAnjunService()) {
        //     return apiResponse(false, $shippingService->name . ' is currently not available.');
        // }

        // if (!setting('bcn_api', null, \App\Models\User::ROLE_ADMIN) && $shippingService->is_bcn_service) {
        //     return apiResponse(false, $shippingService->name . ' is currently not available.');
        // }
        if (Auth::id() != "1233"  && $shippingService->is_anjun_china_service_sub_class) {
            return apiResponse(false, $shippingService->name . ' is currently not available.');
        }

        if (setting('anjun_api', null, \App\Models\User::ROLE_ADMIN)){
            if ($shippingService->service_sub_class == ShippingService::Packet_Mini) {
                return apiResponse(false, $shippingService->name . ' is currently not available.');
            }

            if (in_array($shippingService->service_sub_class,[ShippingService::Packet_Standard,ShippingService::BCN_Packet_Standard])){
                $shippingService = ShippingService::where('service_sub_class', ShippingService::AJ_Packet_Standard)->first();
            }
            if (in_array($shippingService->service_sub_class,[ShippingService::Packet_Express,ShippingService::BCN_Packet_Express])){
                $shippingService = ShippingService::where('service_sub_class', ShippingService::AJ_Packet_Express)->first();
            }
        }
        if (setting('bcn_api', null, \App\Models\User::ROLE_ADMIN)){
            if ($shippingService->service_sub_class == ShippingService::Packet_Mini) {
                return apiResponse(false, $shippingService->name . ' is currently not available.');
            }
            if (in_array($shippingService->service_sub_class,[ShippingService::Packet_Standard,ShippingService::AJ_Packet_Standard])){
                $shippingService = ShippingService::where('service_sub_class', ShippingService::BCN_Packet_Standard)->first();
            }
            if (in_array($shippingService->service_sub_class,[ShippingService::Packet_Express,ShippingService::AJ_Packet_Express])){
                $shippingService = ShippingService::where('service_sub_class', ShippingService::BCN_Packet_Express)->first();
            }
        }
        
        if (setting('correios_api', null, \App\Models\User::ROLE_ADMIN)){
            if (in_array($shippingService->service_sub_class,[ShippingService::BCN_Packet_Standard,ShippingService::AJ_Packet_Standard])){
                $shippingService = ShippingService::where('service_sub_class', ShippingService::Packet_Standard)->first();
            }
            if (in_array($shippingService->service_sub_class,[ShippingService::BCN_Packet_Express,ShippingService::AJ_Packet_Express])){
                $shippingService = ShippingService::where('service_sub_class', ShippingService::Packet_Express)->first();
            }
        }


        if (optional($request->parcel)['measurement_unit'] == 'kg/cm') {
            $volumetricWeight = WeightCalculator::getVolumnWeight($length, $width, $height, 'cm');
            $volumeWeight = round($volumetricWeight > $weight ? $volumetricWeight : $weight, 2);

            if ($shippingService->isCorreiosService() && $volumeWeight > 30) {
                return apiResponse(false, "Your " . $volumeWeight . " kg/cm weight has exceeded the limit. Please check the weight and dimensions. Weight shouldn't be greater than 30 kg/cm");
            }
        } else {
            $volumetricWeight = WeightCalculator::getVolumnWeight($length, $width, $height, 'in');
            $volumeWeight = round($volumetricWeight > $weight ? $volumetricWeight : $weight, 2);

            if ($shippingService->isCorreiosService() && $volumeWeight > 65.15) {
                return apiResponse(false, "Your " . $volumeWeight . " lbs/in weight has exceeded the limit. Please check the weight and dimensions. Weight shouldn't be greater than 66.15 lbs/in");
            }
        }

        if ($shippingService->isGDEService()) {
            $weightLimit = optional($request->parcel)['measurement_unit'] == 'lbs/in' ? UnitsConverter::poundToKg($weight) : $weight;
            if ($weightLimit <= 0.453) {
                $shippingService = ShippingService::where('service_sub_class', ShippingService::GDE_FIRST_CLASS)->first();
            } else {
                $shippingService = ShippingService::where('service_sub_class', ShippingService::GDE_PRIORITY_MAIL)->first();
            }
        }

        if ($shippingService->service_sub_class == ShippingService::GSS_CEP) {
            
            if(optional($request->parcel)['measurement_unit'] == "lbs/in" && $weight > 4.40 || optional($request->parcel)['measurement_unit'] == "kg/cm" && $weight > 2) {
                return apiResponse(false, "Parcel Weight cannot be more than 4.40 LBS / 2 KG. Please Update Your Parcel");
            }
            if($length+$width+$height > $shippingService->max_sum_of_all_sides) {
                return apiResponse(false, "Maximun Pacakge Size: The sum of the length, width and height cannot not be greater than 90 cm (l + w + h <= 90). Please Update Your Parcel");
            }
            $products = collect($request->get('products', []));
            $itemsValue = $products->sum(function ($product) {
                return optional($product)['quantity'] * optional($product)['value'];
            });
            if($itemsValue > 400 ) {
                return apiResponse(false, "Total Parcel Value cannot be more than $400");
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
            $senderStateID = State::where([['code', $senderStateID], ['country_id', $senderCountryID]])->orWhere([['name', $senderStateID], ['country_id', $senderCountryID]])->first()->id;
        }

        if (!is_numeric(optional($request->recipient)['country_id'])) {

            $country = Country::where('code', optional($request->recipient)['country_id'])->orwhere('id', optional($request->recipient)['country_id'])->first();
            $recipientCountryId = $country->id;
        }
        if (!is_numeric(optional($request->recipient)['state_id']) && $stateID !== null) {

            $state = State::where('country_id', $recipientCountryId)->where('code', optional($request->recipient)['state_id'])->orwhere('id', optional($request->recipient)['state_id'])->first();
            $stateID = $state->id;
        }

        if ($shippingService->isDomesticService() && !$this->usShippingService->isAvalaible($shippingService, $volumeWeight)) {
            return apiResponse(false, $this->usShippingService->getError());
        }

        if ($shippingService->isDomesticService() && $recipientCountryId != Country::US) {
            return apiResponse(false, 'this service is availaible for US address only');
        }

        if ($shippingService->isInternationalService() && !$this->usShippingService->isAvailableForInternational($shippingService, $volumeWeight)) {
            return apiResponse(false, $this->usShippingService->getError());
        }

        if ($shippingService->isInternationalService() && $recipientCountryId == Country::US) {
            return apiResponse(false, 'this service is not availaible for US address');
        }

        if (!$this->serviceActive($shippingService)) {
            return apiResponse(false, 'Selected shipping service is not active against your account!!.');
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
                "weight" =>  round(optional($request->parcel)['weight'], 2),
                "length" =>  round(optional($request->parcel)['length'], 2),
                "width" =>   round(optional($request->parcel)['width'], 2),
                "height" =>  round(optional($request->parcel)['height'], 2),
                "is_invoice_created" => true,
                "order_date" => now(),
                "is_shipment_added" => true,
                'status' => Order::STATUS_ORDER,
                'user_declared_freight' => optional($request->parcel)['shipment_value'] ?? 0,
                'sinerlog_tran_id' => optional($request->parcel)['return_option'],

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
                "state_id" => ($recipientCountryId == Order::UK) ? null : $stateID,
                "country_id" => $recipientCountryId
            ]);

            if ($recipientCountryId == Order::CHILE || $recipientCountryId == Order::UK) {
                $order->recipient()->update([
                    "region" => optional($request->recipient)['region'],
                ]);
            }

            $isBattery = false;
            $isPerfume = false;
            foreach ($request->get('products', []) as $product) {
                if (optional($product)['is_battery']) {
                    $isBattery = true;
                }
                if (optional($product)['is_perfume']) {
                    $isPerfume = true;
                }

                $shCode = optional($product)['sh_code'];
                $shCode = getValidShCode(optional($product)['sh_code'], $order->shippingService);

                $order->items()->create([
                    "sh_code" => $shCode,
                    "description" => optional($product)['description'],
                    "quantity" => optional($product)['quantity'],
                    "value" => optional($product)['value'],
                    "contains_battery" => optional($product)['is_battery'],
                    "contains_perfume" => optional($product)['is_perfume'],
                    "contains_flammable_liquid" => optional($product)['is_flameable'],
                ]);
            }
            if ($isBattery === true && $isPerfume === true) {
                throw new \Exception("Please don't use battery and perfume in one parcels", 500);
            }

            $orderValue = collect($request->get('products', []))->sum(function ($item) {
                return $item['value'] * $item['quantity'];
            });

            $order->update([
                'warehouse_number' => $order->getTempWhrNumber(true),
                "order_value" => $orderValue,
                'shipping_service_name' => $order->shippingService->name
            ]);

            if ($recipientCountryId == Order::US && !(!$order->shippingService->isDomesticService() || !$order->shippingService->isInboundDomesticService())) {
                DB::rollback();

                return apiResponse(false, 'this service can not be use against US address');
            }

            if ($order->shippingService->isDomesticService() || $order->shippingService->isInternationalService()) {
                if (!$this->usShippingService->getUSShippingServiceRate($order)) {
                    DB::rollback();
                    return apiResponse(false, $this->usShippingService->getError());
                }
            }

            if ($order->shippingService->isGSSService()) {
                if (!$this->usShippingService->getGSSRates($order)) {
                    DB::rollback();
                    return apiResponse(false, $this->usShippingService->getError());
                }
            }

            $order->syncServices(optional($request->parcel)['services'] ?? []);

            $order->doCalculations();

            DB::commit();
            return apiResponse(true, "Parcel Created", OrderResource::make($order));
        } catch (\Exception $ex) {
            DB::rollback();
            return apiResponse(false, $ex->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $parcel = Order::where('user_id', Auth::id())->where('id', $id)->first();
        if ($parcel) {
            return apiResponse(true, "Get Parcel Data successfully", OrderResource::make($parcel));
        }
        return apiResponse(false, "The parcel doesn't exist", null);
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
        if (Auth::id() != $parcel->user_id) {
            return apiResponse(false, 'Order not found');
        }
        if ($parcel->isPaid()) {
            return apiResponse(false, 'order can not be updated once payment has been paid');
        }
        $weight = optional($request->parcel)['weight'] ?? 0;
        $length = optional($request->parcel)['length'] ?? 0;
        $width = optional($request->parcel)['width'] ?? 0;
        $height = optional($request->parcel)['height'] ?? 0;

        $shippingService = ShippingService::find($request->parcel['service_id'] ?? null);

        if (!$shippingService) {
            return apiResponse(false, 'Shipping service not found.');
        }
        if (!setting('anjun_api', null, \App\Models\User::ROLE_ADMIN) && $shippingService->isAnjunService()) {
            return apiResponse(false, $shippingService->name . ' is currently not available.');
        }

        if (!setting('bcn_api', null, \App\Models\User::ROLE_ADMIN) && $shippingService->is_bcn_service) {
            return apiResponse(false, $shippingService->name . ' is currently not available.');
        }
        if (Auth::id() != "1233"  && $shippingService->is_anjun_china_service_sub_class) {
            return apiResponse(false, $shippingService->name . ' is currently not available.');
        }

        if (setting('anjun_api', null, \App\Models\User::ROLE_ADMIN)) {
            if ($shippingService->service_sub_class == ShippingService::Packet_Mini) {
                return apiResponse(false, $shippingService->name . ' is currently not available.');
            }

            if ($shippingService->service_sub_class == ShippingService::Packet_Standard) {
                $shippingService = ShippingService::where('service_sub_class', ShippingService::AJ_Packet_Standard)->first();
            }

            if ($shippingService->service_sub_class == ShippingService::AJ_Packet_Express) {
                $shippingService = ShippingService::where('service_sub_class', ShippingService::AJ_Packet_Express)->first();
            }
        }
        if (setting('bcn_api', null, \App\Models\User::ROLE_ADMIN)) {
            if ($shippingService->service_sub_class == ShippingService::Packet_Mini) {
                return apiResponse(false, $shippingService->name . ' is currently not available.');
            }
            if (in_array($shippingService->service_sub_class, [ShippingService::Packet_Standard, ShippingService::AJ_Packet_Standard])) {
                $shippingService = ShippingService::where('service_sub_class', ShippingService::BCN_Packet_Standard)->first();
            }
            if (in_array($shippingService->service_sub_class, [ShippingService::AJ_Packet_Express, ShippingService::AJ_Packet_Express])) {
                $shippingService = ShippingService::where('service_sub_class', ShippingService::BCN_Packet_Express)->first();
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


        if($shippingService->isDomesticService() && !$this->usShippingService->isAvalaible($shippingService, $volumeWeight))
        {
            return apiResponse(false, $this->usShippingService->getError());

            if ($recipientCountryId != Country::US) {
                return apiResponse(false, 'this service is availaible for US address only');
            }
        }

        if($shippingService->isInternationalService() && !$this->usShippingService->isAvailableForInternational($shippingService, $volumeWeight)){
            return apiResponse(false, $this->usShippingService->getError());

            if ($recipientCountryId == Country::US) {
                return apiResponse(false, 'this service is not availaible for US address');
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
                "order_value" => $orderValue,
                'shipping_service_name' => $parcel->shippingService->name
            ]);

            if ($shippingService->isDomesticService() || $shippingService->isInternationalService()) {
                if(!$this->usShippingService->getUSShippingServiceRate($parcel))
                {
                    DB::rollback();
                    return apiResponse(false, $this->usShippingService->getError());
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
        if(Auth::id() != $parcel->user_id){
            return apiResponse(false,'Order not found');
        }

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

    public function serviceActive($shippingService)
    {
        if (in_array($shippingService->service_sub_class,[ShippingService::Packet_Standard,ShippingService::AJ_Packet_Standard,ShippingService::AJ_Standard_CN,ShippingService::BCN_Packet_Standard])) {
            $shippingService = ShippingService::where('service_sub_class', ShippingService::Packet_Standard)->first();
        }
        if (in_array($shippingService->service_sub_class,[ShippingService::Packet_Express,ShippingService::AJ_Packet_Express,ShippingService::AJ_Express_CN,ShippingService::BCN_Packet_Express])) {
            $shippingService = ShippingService::where('service_sub_class', ShippingService::Packet_Express)->first();
        }

        $profitSetting = ProfitSetting::where('user_id', Auth::id())
            ->where('service_id',$shippingService->id)
            ->where('package_id', '!=', null)
            ->first();
        if($profitSetting){
            return true;
        }
        if( $shippingService->isOfUnitedStates() ||
            $shippingService->isDomesticService() ||
            $shippingService->isInternationalService() ||
            $shippingService->isInboundDomesticService() ||
            $shippingService->isGSSService() ||
            $shippingService->isGDEService() )
        {
            return true;
        }
        return false;
    }

}
