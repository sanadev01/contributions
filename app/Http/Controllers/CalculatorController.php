<?php

namespace App\Http\Controllers;

use App\Http\Requests\Calculator\CalculatorRequest;
use App\Models\User;
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

        
        if(auth()->user()->id == 1 ){
            $adminId =  User::ROLE_ADMIN;; 
        }
        //    if(setting('anjun_api', null, $adminId)){
        //     $shippingServices = $shippingServices->where('name','Packet Express')->orwhere('name','Anjun Express');
        //    }else{
        //     $shippingServices = $shippingServices->where('name','PostNL')->orwhere('name','Global eParcel');

        //    }  

        return view('calculator.show', compact('order', 'shippingServices', 'weightInOtherUnit', 'chargableWeight','adminId'));

    }

}
