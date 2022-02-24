<?php

namespace App\Http\Controllers;


use App\Models\State;
use App\Models\Country;

use Illuminate\Http\Request;
use App\Services\Converters\UnitsConverter;
use App\Http\Requests\Calculator\USCalculatorRequest;
use App\Repositories\Calculator\USCalculatorRepository;

class UPSCalculatorController extends Controller
{
    public $error;


    public function index()
    {
        $states = State::query()->where("country_id", Country::US)->get(["name","code","id"]);
        return view('upscalculator.index', compact('states'));
    }

    public function store(USCalculatorRequest $request, USCalculatorRepository $usCalculatorRepository)
    {   
        
        $tempOrder = $usCalculatorRepository->handle($request);
        $upsShippingServices = $usCalculatorRepository->getUPSShippingServices($tempOrder);
        $usCalculatorRepository->setUserUPSProfit();

        $apiRates = $usCalculatorRepository->getUPSRates($upsShippingServices, $tempOrder);
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
        $tempOrder = collect($tempOrder);
        
        return view('uscalculator.index', compact('apiRates','ratesWithProfit','tempOrder', 'weightInOtherUnit', 'chargableWeight', 'userLoggedIn', 'shippingServiceTitle'));
    }
}
