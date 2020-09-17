<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipient;
use App\Models\Order;
use App\Models\ShippingService;
use App\Models\State;
use App\Models\User;
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


        return view('calculator.show', compact('shippingServices','order'));

        // if ( $request->unit == 'kg/cm' ){ 
        //     $weightInOtherUnit = UnitsConverter::kgToPound($request->weight);
        //     UnitsConverter::inToCm()
        //     UnitsConverter::cmToIn()
        // }else{
        //     $weightInOtherUnit = UnitsConverter::poundToKg($request->weight);
        // }
        // $shippingService->name ;
        // $order->weight 
        // $shippingServices->getRateFor($order); will give rate for service

    }

}
