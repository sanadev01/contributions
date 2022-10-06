<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\State;
use App\Models\Country;
use Illuminate\Support\Facades\Cache;
use App\Services\Converters\UnitsConverter;
use App\Http\Requests\Calculator\USCalculatorRequest;
use App\Repositories\Calculator\USCalculatorRepository;

class USCalculatorController extends Controller
{
    public function index()
    {
        $states = Cache::remember('states', Carbon::now()->addDay(), function () {
            return State::query()->where('country_id', Country::US)->get(['name','code','id']);
        });

        $userId = (auth()->check()) ? auth()->user()->id : null;
        $cc = 'US';
        return view('uscalculator.calculator', compact('states', 'userId', 'cc'));
    }

    public function store(USCalculatorRequest $request, USCalculatorRepository $usCalculatorRepository)
    {
        $tempOrder = $usCalculatorRepository->handle($request);
        $shippingServices = $usCalculatorRepository->getShippingServices();

        $apiRates = $usCalculatorRepository->getRates();
        $ratesWithProfit = $usCalculatorRepository->getRatesWithProfit();

        $error = $usCalculatorRepository->getError();

        if($shippingServices->isEmpty()){
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

        $shippingServiceTitle = 'US Services';
        $tempOrder = collect($tempOrder);

        return view('uscalculator.index', compact('apiRates','ratesWithProfit','tempOrder', 'weightInOtherUnit', 'chargableWeight', 'userLoggedIn', 'shippingServiceTitle'));
    }
}
