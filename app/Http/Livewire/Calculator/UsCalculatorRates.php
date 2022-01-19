<?php

namespace App\Http\Livewire\Calculator;

use Livewire\Component;

class UsCalculatorRates extends Component
{
    public $apiRates;
    public $ratesWithProfit;
    public $order;
    public $weightInOtherUnit;
    public $chargableWeight;
    public $userLoggedIn;
    public $shippingServiceTitle;
    public $serviceResponse = false;

    public function mount($apiRates, $ratesWithProfit, $order, $weightInOtherUnit, $chargableWeight, $userLoggedIn, $shippingServiceTitle)
    {
        $this->apiRates = $apiRates;
        $this->ratesWithProfit = $ratesWithProfit;
        $this->order = $order;
        $this->weightInOtherUnit = $weightInOtherUnit;
        $this->chargableWeight = $chargableWeight;
        $this->userLoggedIn = $userLoggedIn;
        $this->shippingServiceTitle = $shippingServiceTitle;
    }

    public function render()
    {
        return view('livewire.calculator.us-calculator-rates');
    }
}
