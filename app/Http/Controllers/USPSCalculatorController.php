<?php

namespace App\Http\Controllers;

use App\Models\State;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Services\Converters\UnitsConverter;
use App\Http\Requests\Calculator\USCalculatorRequest;
use App\Repositories\Calculator\USCalculatorRepository;

class USPSCalculatorController extends Controller
{
    public $error;
    public $shipping_rates = [];
    public $user_api_profit;
    public $userLoggedIn = false;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $states = State::query()->where("country_id", Country::US)->get(["name","code","id"]);
        return view('uspscalculator.index', compact('states'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(USCalculatorRequest $request, USCalculatorRepository $usCalculatorRepository)
    {
        $tempOrder = $usCalculatorRepository->handle($request);
        $uspsShippingServices = $usCalculatorRepository->getUSPSShippingServices($tempOrder);
        $usCalculatorRepository->setUserUSPSProfit();

        $apiRates = $usCalculatorRepository->getUSPSRates($uspsShippingServices, $tempOrder);
        $ratesWithProfit = $usCalculatorRepository->getUSPSRatesWithProfit();
        
        $error = $usCalculatorRepository->getError();

        if($uspsShippingServices->isEmpty()){
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

        $shippingServiceTitle = 'USPS';

        return view('uscalculator.index', compact('apiRates','ratesWithProfit','tempOrder', 'weightInOtherUnit', 'chargableWeight', 'userLoggedIn', 'shippingServiceTitle'));
    }
}
