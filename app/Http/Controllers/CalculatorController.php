<?php

namespace App\Http\Controllers;

use App\Http\Requests\Calculator\CalculatorRequest;
use App\Repositories\Calculator\CalculatorRepository;
use Illuminate\Support\Facades\Auth;

class CalculatorController extends Controller
{
    public function index()
    {
        if(Auth::check())
            return view('calculator.index');
        else
            return view('calculator.guest-index');
    }

    public function store(CalculatorRequest $request, CalculatorRepository $calculatorRepository)
    {
        $order = $calculatorRepository->handel($request);
        $shippingServices =  $calculatorRepository->getShippingService();
        $chargableWeight = $calculatorRepository->getChargableWeight();
        $weightInOtherUnit = $calculatorRepository->getWeightInOtherUnit($request);
        dd(compact('order', 'shippingServices', 'weightInOtherUnit', 'chargableWeight'));
        if(Auth::check())
            return view('calculator.show', compact('order', 'shippingServices', 'weightInOtherUnit', 'chargableWeight'));
        else
            return view('calculator.guest-show', compact('order', 'shippingServices', 'weightInOtherUnit', 'chargableWeight'));
    }

}
