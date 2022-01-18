<?php

namespace App\Http\Controllers;


use App\Models\State;
use Illuminate\Http\Request;

use App\Services\Converters\UnitsConverter;
use App\Repositories\UPSCalculatorRepository;
use App\Http\Requests\Calculator\UPSCalculatorRequest;
use App\Repositories\Calculator\USCalculatorRepository;

class UPSCalculatorController extends Controller
{
    public $error;


    public function index()
    {
        $states = State::query()->where("country_id", 250)->get(["name","code","id"]);
        return view('upscalculator.index', compact('states'));
    }

    public function store(UPSCalculatorRequest $request, USCalculatorRepository $usCalculatorRepository)
    {   
        
        $order = $usCalculatorRepository->handle($request);
        $upsShippingServices = $usCalculatorRepository->getUPSShippingServices($order);
        $usCalculatorRepository->setUserUPSProfit();

        $apiRates = $usCalculatorRepository->getUPSRates($upsShippingServices, $order);
        $ratesWithProfit = $usCalculatorRepository->getUPSRatesWithProfit();
        
        $error = $usCalculatorRepository->getError();
        
        if($upsShippingServices->isEmpty()){
            $error = 'Shipping Service not Available for the Country you have selected';
        }

        if($ratesWithProfit == null){
            session()->flash('alert-danger', $error);
        }

        $userLoggedIn = $usCalculatorRepository->getUserLoggedInStatus();
        $chargableWeight = $usCalculatorRepository->getchargableWeight();

        if ($request->unit == 'kg/cm' ){
            $weightInOtherUnit = UnitsConverter::kgToPound($chargableWeight);
        }else{
            $weightInOtherUnit = UnitsConverter::poundToKg($chargableWeight);
        }

        $shippingServiceTitle = 'UPS';
        
        return view('uscalculator.index', compact('apiRates','ratesWithProfit','order', 'weightInOtherUnit', 'chargableWeight', 'userLoggedIn', 'shippingServiceTitle'));
    }

    public function buy_ups_label(Request $request)
    {
        $ups_calculatorRepository = new UPSCalculatorRepository();
        $order = $ups_calculatorRepository->handle($request);

        $error = $ups_calculatorRepository->getUPSErrors();

        if($error != null)
        {
            return (Array)[
                'success' => false,
                'message' => $error,
            ]; 
        }

        return (Array)[
            'success' => true,
            'message' => 'UPS label has been generated successfully',
            'path' => route('admin.orders.label.index', $order->id)
        ]; 
    }
}
