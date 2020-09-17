<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipient;
use App\Models\Order;
use App\Models\ShippingService;
use App\Models\State;
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
        $order->user = Auth::user() ? Auth::user() :  User::where('role','admin')->first();
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

        dd($shippingServices);

        // In view
        // PAss order and services to view

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




        $address = new Address();
        $address->state = $request->state;
        $address->country = Country::find($request->country);

        $shipment = new Order();
        $shipment->id = microtime(true);
        $shipment->width = $request->form['width'];
        $shipment->height = $request->form['height'];
        $shipment->length = $request->form['length'];
        $shipment->weight = $request->form['weight'];
        $shipment->unit = $request->form['measurement_unit'];

        $order = new Order();
        $order->user = Auth::user() ? Auth::user() :  User::where('role','admin')->first();
        $order->shipment = $shipment;
        $order->address = $address;

        $bpsRateService = new BpsRatesCalculator($order);
        $packagePlusRateService = new PackagePlusRatesCalculator($order);

        $availableServices = [];

        if ($bpsRateService->isAvailable()) {
            $availableServices[] = [
                'name' => $bpsRateService->getName(),
                'price' => round($bpsRateService->getRate(), 2).' USD',
                'weight' => number_format($bpsRateService->getWeight(), 2)
            ];
        }

        if ($packagePlusRateService->isAvailable()) {
            $availableServices[] = [
                'name' => $packagePlusRateService->getName(),
                'price' => round($packagePlusRateService->getRate(), 2).' USD',
                'weight' => number_format($packagePlusRateService->getWeight(), 2)
            ];
        }

        return  $availableServices;
    }

}
