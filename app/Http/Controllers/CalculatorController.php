<?php

namespace App\Http\Controllers;

use App\Http\Requests\Calculator\CalculatorRequest;
use App\Repositories\Calculator\CalculatorRepository;
class CalculatorController extends Controller
{
    public function index()
    {
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
