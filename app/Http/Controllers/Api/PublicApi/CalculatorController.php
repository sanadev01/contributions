<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Models\ShCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Calculator\CalculatorRequest;
use App\Repositories\Calculator\CalculatorRepository;

class CalculatorController extends Controller
{
    public function index(Request $request, CalculatorRepository $calculatorRepository)
    {
        $order = $calculatorRepository->handel($request);
        $shippingServices =  $calculatorRepository->getShippingService();
        $chargableWeight = $calculatorRepository->getChargableWeight();
        $weightInOtherUnit = $calculatorRepository->getWeightInOtherUnit($request);

        $data = [];
        foreach ($shippingServices as $shippingService){
            $name = $shippingService->name;
            if($order->measurement_unit == 'kg/cm'){
                $weight="$chargableWeight Kg ( $weightInOtherUnit lbs )";
            }else{
                $weight="$chargableWeight lbs ( $weightInOtherUnit kg )";
            }
            $rate = $shippingService->getRateFor($order,true,true);
            array_push($data,['name'=>$name, 'weight'=>$weight, 'cost' => $rate]);
        }
        if($data){
            return apiResponse(true,'Rates found Successfully',$data);
        }
        return apiResponse(false,"Rates do not found against your account, Please Contact HD support");
    }
}
