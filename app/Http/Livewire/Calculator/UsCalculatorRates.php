<?php

namespace App\Http\Livewire\Calculator;

use Livewire\Component;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Repositories\Calculator\USCalculatorRepository;
use FontLib\TrueType\Collection;

class UsCalculatorRates extends Component
{
    public $apiRates;
    public $ratesWithProfit;
    public $tempOrder;
    public $weightInOtherUnit;
    public $chargableWeight;
    public $userLoggedIn;
    public $shippingServiceTitle;
    public $serviceResponse = false;

    public $error;
    public $selectedService;
    private $selectedServiceCost;
    private $order;


    public function mount($apiRates, $ratesWithProfit, $tempOrder, $weightInOtherUnit, $chargableWeight, $userLoggedIn, $shippingServiceTitle)
    {
        $this->apiRates = $apiRates;
        $this->ratesWithProfit = $ratesWithProfit;
        $this->tempOrder = $tempOrder->toArray();
        $this->weightInOtherUnit = $weightInOtherUnit;
        $this->chargableWeight = $chargableWeight;
        $this->userLoggedIn = $userLoggedIn;
        $this->shippingServiceTitle = $shippingServiceTitle;
    }

    public function render()
    {
        return view('livewire.calculator.us-calculator-rates');
    }

    public function getSenderLabel(USCalculatorRepository $usCalculatorRepository)
    {
        if (!$this->selectedService) {
            return $this->addError('selectedService', 'select service please.');
        }
        
        if ($this->getCostOfSelectedService() > getBalance())
        {
            $this->error = 'Not Enough Balance. Please Recharge your account';
            return false;
        }

        $order = $usCalculatorRepository->execute($this->createRequest());
        $this->error = $usCalculatorRepository->getError();

        if (!$this->error && $order) {
            return redirect()->route('admin.orders.label.index', $order->id);
        }
    }

    private function getCostOfSelectedService()
    {
        Arr::where($this->ratesWithProfit, function ($value, $key) {
            if($value['service_sub_class'] == $this->selectedService)
            {
                return $this->selectedServiceCost = $value['rate'];
            }
        });
    }

    private function createRequest()
    {
        return new Request([
            'temp_order' => $this->tempOrder,
            'approximate_cost' => $this->selectedServiceCost,
            'service_sub_class' => $this->selectedService,
        ]);
    }
}
