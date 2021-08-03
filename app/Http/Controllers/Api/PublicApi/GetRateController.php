<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Models\User;
use App\Models\Order;
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
        // try{
            $rules = [
                'country_id' => 'required|numeric|exists:countries,id',
                'state_id' => 'required|exists:states,id',
                'height' => 'sometimes|numeric',
                'width' => 'sometimes|numeric',
                'length' => 'sometimes|numeric',
                'unit' => 'required|in:lbs/in,kg/cm',
            ];
            if($request->unit == 'kg/cm'){
                $rules['weight'] = 'sometimes|numeric|max:30';
            }else{
                $rules['weight'] = 'sometimes|numeric|max:66.15';
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

            $originalWeight =  $request->weight;
            if ( $request->unit == 'kg/cm' ){
                $volumetricWeight = WeightCalculator::getVolumnWeight($request->length,$request->width,$request->height,'cm');
                $chargableWeight = round($volumetricWeight >  $originalWeight ? $volumetricWeight :  $originalWeight,2);
            }else{
                $volumetricWeight = WeightCalculator::getVolumnWeight($request->length,$request->width,$request->height,'in');
                $chargableWeight = round($volumetricWeight >  $originalWeight ? $volumetricWeight :  $originalWeight,2);
            }
            $recipient = new Recipient();
            $recipient->state_id = $request->state_id;
            $recipient->country_id = $request->country_id;

            $order = new Order();
            $order->user = Auth::user() ? Auth::user() :  User::where('role_id',1)->first();
            $order->width = $request->width;
            $order->height = $request->height;
            $order->length = $request->length;
            $order->weight = $request->weight;
            $order->measurement_unit = $request->unit;
            $order->recipient = $recipient;

            $shippingServices = collect();
            foreach (ShippingService::query()->active()->get() as $shippingService) {
                
                if ( $shippingService->isAvailableFor($order) ){
                    $shippingServices->push($shippingService);
                }else{
                    session()->flash('alert-danger',"Shipping Service not Available Error:{$shippingService->getCalculator($order)->getErrors()}");
                }
            }

            if ($request->unit == 'kg/cm' ){
                $weightInOtherUnit = UnitsConverter::kgToPound($chargableWeight);
                $weightInOtherUnit = $weightInOtherUnit.' lbs/in';
                $chargableWeight = $chargableWeight.' kg/cm';

            }else{
                $weightInOtherUnit = UnitsConverter::poundToKg($chargableWeight);
                $weightInOtherUnit = $weightInOtherUnit.' kg/cm';
                $chargableWeight = $chargableWeight.' lbs/in';
            }

            $getRate =collect();
            
            foreach($shippingServices as $shippingService){
                $getRate->push([
                    'shippingServices'  => $shippingService->name,
                    // 'weightInOtherUnit'  => $weightInOtherUnit,
                    'Weight'  => $chargableWeight,
                    'cost'  => $shippingService->getRateFor($order,true,true),
                ]);
            }
            return apiResponse(true,$getRate->count().' Services Rate Found against your Weight',$getRate);
        // } catch (\Exception $ex) {
        //    return apiResponse(false,$ex->getMessage());
        // }
    }
}
