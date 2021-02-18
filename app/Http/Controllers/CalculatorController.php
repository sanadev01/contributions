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
        $this->validate(
            $request,
            [
                'country_id' => 'required|numeric|exists:countries,id',
                'state_id' => 'required|exists:states,id',
                'weight' => 'sometimes|numeric',
                'height' => 'sometimes|numeric',
                'width' => 'sometimes|numeric',
                'length' => 'sometimes|numeric',
                'unit' => 'required|in:lbs/in,kg/cm',
            ],
            [
                'country_id' => 'Please Select A country',
                'state_id' => 'Please Select A state',
                'weight' => 'Please Enter weight',
                'height' => 'Please Enter height',
                'width' => 'Please Enter width',
                'length' => 'Please Enter length',
                'unit' => 'Please Select Measurement Unit ',
            ]
        );

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
        }else{
            $weightInOtherUnit = UnitsConverter::poundToKg($chargableWeight);
        }

        return view('calculator.show', compact('order', 'shippingServices', 'weightInOtherUnit', 'chargableWeight'));

    }

}
