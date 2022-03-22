<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipient;
use App\Models\Order;
use App\Models\ShippingService;
use App\Models\State;
use App\Models\User;
use App\Services\Converters\UnitsConverter;
use App\Services\Calculators\WeightCalculator;
use Auth;

class CalculatorController extends Controller
{
    public function index()
    {
        return view('calculator.index');
    }

    public function store(Request $request)
    {
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

        if ( $request->unit == 'kg/cm' && !$request->weight_discount){
            $volumetricWeight = WeightCalculator::getVolumnWeight($request->length,$request->width,$request->height,'cm');
            $chargableWeight = round($volumetricWeight >  $originalWeight ? $volumetricWeight :  $originalWeight,2);
        }elseif($request->unit == 'lbs/in' && !$request->weight_discount){
            $volumetricWeight = WeightCalculator::getVolumnWeight($request->length,$request->width,$request->height,'in');
            $chargableWeight = round($volumetricWeight >  $originalWeight ? $volumetricWeight :  $originalWeight,2);
        }else{
            $chargableWeight = $request->discount_volume_weight;
        }


        $recipient = new Recipient();
        $recipient->state_id = $request->state_id;
        $recipient->country_id = $request->country_id;

        $order = new Order();
        $order->id = 1;
        $order->user = Auth::user() ? Auth::user() :  User::where('role_id',1)->first();
        $order->width = $request->width;
        $order->height = $request->height;
        $order->length = $request->length;
        $order->weight = $request->weight;
        $order->measurement_unit = $request->unit;
        $order->weight_discount = $request->weight_discount;
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
        }else{
            $weightInOtherUnit = UnitsConverter::poundToKg($chargableWeight);
        }

        return view('calculator.show', compact('order', 'shippingServices', 'weightInOtherUnit', 'chargableWeight'));

    }

}
