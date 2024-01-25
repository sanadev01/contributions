<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Models\User;
use App\Models\Order;
use App\Models\State;
use App\Models\Country;
use App\Models\Recipient;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\Converters\UnitsConverter;
use App\Services\Calculators\WeightCalculator;

class GetRateController extends Controller
{
    public function __invoke(Request $request)
    {
            $rules = [
                'height' => 'sometimes|numeric',
                'width' => 'sometimes|numeric',
                'length' => 'sometimes|numeric',
                'unit' => 'required|in:lbs/in,kg/cm',
            ];

            if($request->unit == 'kg/cm'){
                if($request->country_id == 30 || $request->country_id == "BR"){
                    $rules['weight'] = 'sometimes|numeric|max:30';    
                }else{
                    $rules['weight'] = 'sometimes|numeric|max:50';
                }
            }else{
                if($request->country_id == 30 || $request->country_id == "BR"){
                    $rules['weight'] = 'sometimes|numeric|max:66.15';    
                }else{
                    $rules['weight'] = 'sometimes|numeric|max:110.231';
                }
            }
            if (is_numeric( $request->country_id)){
                $rules["country_id"] = "required|exists:countries,id";
            }else{
                $rules["country_id"] = "required|exists:countries,code";
            }
            if (is_numeric( $request->state_id)){
                $rules["state_id"] = "required|exists:states,id";
            }else{
                $rules["state_id"] = "required|exists:states,code";
            }

            $message = [
                'country_id' => 'Please Select A country',
                'state_id' => 'Please Select A state',
                'weight' => 'Please Enter weight',
                'weight.max' => 'weight exceed the delivery of Correios',
                'height' => 'Please Enter height',
                'width' => 'Please Enter width',
                'length' => 'Please Enter length',
                'unit' => 'Please Select Measurement Unit ',
            ];
            
            $this->validate($request, $rules, $message);
            
        try{
            $countryID = $request->country_id;
            $stateID = $request->state_id;
            
            if (!is_numeric( $request->country_id)){
                
                $country = Country::where('code', $request->country_id)->orwhere('id', $request->country_id)->first();
                $countryID = $country->id;
            }
            if (!is_numeric( $request->state_id)){

                $state = State::where('country_id', $countryID)->where('code', $request->state_id)->orwhere('id', $request->state_id)->first();
                $stateID = $state->id;
            }

            $originalWeight =  $request->weight;
            if ( $request->unit == 'kg/cm' ){
                $volumetricWeight = WeightCalculator::getVolumnWeight($request->length,$request->width,$request->height,'cm');
                $chargableWeight = round($volumetricWeight >  $originalWeight ? $volumetricWeight :  $originalWeight,2);
            }else{
                $volumetricWeight = WeightCalculator::getVolumnWeight($request->length,$request->width,$request->height,'in');
                $chargableWeight = round($volumetricWeight >  $originalWeight ? $volumetricWeight :  $originalWeight,2);
            }
            $recipient = new Recipient();
            $recipient->state_id = $stateID;
            $recipient->country_id = $countryID;

            $order = new Order();
            $order->user = Auth::user() ? Auth::user() :  User::where('role_id',1)->first();
            $order->width = $request->width;
            $order->height = $request->height;
            $order->length = $request->length;
            $order->weight = $request->weight;
            $order->measurement_unit = $request->unit;
            $order->recipient = $recipient;
            $order->id = 1;
            \Log::info($order);
            // $shippingServices = collect();
            // foreach (ShippingService::query()->active()->get() as $shippingService) {
                
            //     if ( $shippingService->isAvailableFor($order) ){
            //         $shippingServices->push($shippingService);
            //     }else{
            //         session()->flash('alert-danger',"Shipping Service not Available Error:{$shippingService->getCalculator($order)->getErrors()}");
            //     }
            // }

            if ($request->unit == 'kg/cm' ){
                $weightInOtherUnit = UnitsConverter::kgToPound($chargableWeight);
                $weightInOtherUnit = $weightInOtherUnit.' lbs/in';
                $chargableWeight = $chargableWeight.' kg/cm';

            }else{
                $weightInOtherUnit = UnitsConverter::poundToKg($chargableWeight);
                $weightInOtherUnit = $weightInOtherUnit.' kg/cm';
                $chargableWeight = $chargableWeight.' lbs/in';
            }

            $getRate = collect();
            $corrieosServices = [ShippingService::AJ_Standard_CN, ShippingService::AJ_Express_CN, ShippingService::Packet_Standard, ShippingService::Packet_Express, ShippingService::BCN_Packet_Standard, ShippingService::BCN_Packet_Express, ShippingService::AJ_Packet_Standard, ShippingService::AJ_Packet_Express];
            $activeServices = $this->getActiveService();
            foreach (ShippingService::query()->active()->get() as $shippingService) {
                $shippingService->cacheCalculator = false;
                if ($shippingService->isAvailableFor($order)) {
                    if (
                        !in_array($shippingService->service_sub_class, $corrieosServices) ||
                        in_array($shippingService->service_sub_class, $activeServices)
                    ) {
                        $getRate->push([
                            'shippingServices' => $shippingService->name,
                            'Weight'           => $chargableWeight,
                            'cost'             => $shippingService->getRateFor($order, true, false),
                        ]);
                    }
                }
            }
            return apiResponse(true,$getRate->count().' Services Rate Found against your Weight',$getRate);
        } catch (\Exception $ex) {
           return apiResponse(false,$ex->getMessage());
        }
    }

    public function getActiveService()
    {        
        if (setting('china_anjun_api', null, User::ROLE_ADMIN)) {
            return [ShippingService::AJ_Standard_CN, ShippingService::AJ_Express_CN];
        } else if (setting('correios_api', null, User::ROLE_ADMIN)) {
            return [ShippingService::Packet_Standard, ShippingService::Packet_Express];
        } else if (setting('bcn_api', null, User::ROLE_ADMIN)) {
            return [ShippingService::BCN_Packet_Standard, ShippingService::BCN_Packet_Express];
        } else if (setting('anjun_api', null, User::ROLE_ADMIN)) {
            return [ShippingService::AJ_Packet_Standard, ShippingService::AJ_Packet_Express];
        }
    }
}
