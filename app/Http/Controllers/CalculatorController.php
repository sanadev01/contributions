<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CalculatorController extends Controller
{
    public function index()
    {   
        return view('calculator.index');
    }

    public function store(Request $request)
    {
        $request->merge([
            'form' => [
                'width' => $request->form['width']??0,
                'height' => $request->form['height']??0,
                'length' => $request->form['length']??0,
                'weight' => $request->form['weight']??0,
                'selectedUnit' => $request->form['selectedUnit']
            ]
        ]);
        $this->validate(
            $request,
            [
                'country' => 'required|numeric|exists:countries,id',
                'state' => 'required|exists:states,code',
                'form' => 'required|array',
                'form.weight' => 'sometimes|numeric',
                'form.height' => 'sometimes|numeric',
                'form.width' => 'sometimes|numeric',
                'form.length' => 'sometimes|numeric',
                'form.selectedUnit' => 'required|in:lbs/in,kg/cm',
            ],
            [
                'country' => 'Please Select A country',
                'state' => 'Please Select A state',
                'form.weight' => 'Please Enter weight',
                'form.height' => 'Please Enter height',
                'form.width' => 'Please Enter width',
                'form.length' => 'Please Enter length',
                'form.selectedUnit' => 'Please Select Measurement Unit ',
            ]
        );

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
