<?php

namespace App\Http\Controllers;

use App\Http\Requests\Calculator\CalculatorRequest;
use App\Repositories\Calculator\CalculatorRepository;
use Illuminate\Support\Facades\Auth;

class CalculatorController extends Controller
{
    public function index()
    {
            session()->flash('alert-danger',null);
            return view('calculator.index');
    }

    public function store(CalculatorRequest $request, CalculatorRepository $calculatorRepository)
    {
        $order = $calculatorRepository->handel($request);
        $shippingServices =  $calculatorRepository->getShippingService();
        $chargableWeight = $calculatorRepository->getChargableWeight();
        $weightInOtherUnit = $calculatorRepository->getWeightInOtherUnit($request);
        return view('calculator.show', compact('order', 'shippingServices', 'weightInOtherUnit', 'chargableWeight'));
    }

}
