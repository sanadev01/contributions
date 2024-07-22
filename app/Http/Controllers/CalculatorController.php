<?php

namespace App\Http\Controllers;

use App\Http\Requests\Calculator\CalculatorRequest;
use App\Models\ShippingService;
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
        
         // Calculate rating for each shipping service and update the data structure
         $maxOrderCount = PHP_INT_MIN; 
         // Iterate over each shipping service to find the maximum order count
         foreach ($shippingServices as $service) {
             $order_count = $service->orders()->count();
             if ($order_count > $maxOrderCount) {
                 $maxOrderCount = $order_count;
             }
         }
         foreach ($shippingServices as &$service) {
           $service['rating'] = $this->calculateRating($service->orders()->count(),$maxOrderCount);
        } 
        $chargableWeight = $calculatorRepository->getChargableWeight();
        $weightInOtherUnit = $calculatorRepository->getWeightInOtherUnit($request);
        return view('calculator.show', compact('order', 'shippingServices', 'weightInOtherUnit', 'chargableWeight'));
    }
    function calculateRating($orderCount, $maxOrderCount) {
        $rating = ($orderCount / $maxOrderCount) * 5;
        return round($rating, 1);
    }

}
