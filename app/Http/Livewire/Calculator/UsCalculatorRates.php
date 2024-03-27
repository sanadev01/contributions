<?php

namespace App\Http\Livewire\Calculator;

use Livewire\Component;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use App\Repositories\Calculator\USCalculatorRepository;

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

    public $serviceError;
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

    public function getLabel($subClass)
    {
        $this->selectedService = $subClass;
        $usCalculatorRepository = new  USCalculatorRepository();
        if (!$this->selectedService){
            $this->addError('selectedService', 'select service please.');
            return false;
        }

        if (!$this->userLoggedIn) {
            $this->addError('serviceError', 'Please login to continue.');
            return false;
        }

        if ($this->getCostOfSelectedService() > getBalance()) {
            $this->addError('serviceError', 'insufficient balance, please recharge your account.');
            return false;
        }

        if ($this->selectedServiceEnabledForUser()) {
            $order = $usCalculatorRepository->executeForLabel($this->createRequest());
            $this->addError('serviceError', $usCalculatorRepository->getError());

            if ($order) {
                return redirect()->route('admin.orders.label.index', $order->id);
            }
        }
    }

    public function downloadRates()
    {
        if ($this->ratesWithProfit) {
            $usCalculatorRepository = new USCalculatorRepository();

            return $usCalculatorRepository->download($this->ratesWithProfit, $this->tempOrder, $this->chargableWeight, $this->weightInOtherUnit);
        }
    }

    private function selectedServiceEnabledForUser()
    {
        if ($this->userLoggedIn) {
            $serviceTitle = $this->getServiceTitle();

            if (setting($serviceTitle, null, auth()->user()->id)) {
                return true;
            }

            $this->addError('serviceError', $serviceTitle . ' service is not enabled for your account, contact admin please');
            return false;
        }
        $this->addError('serviceError', 'Please login to continue');
        return false;
    }

    private function getCostOfSelectedService()
    {
        Arr::where($this->ratesWithProfit, function ($value, $key) {
            if ($value['service_sub_class'] == $this->selectedService) {
                $this->selectedServiceCost = $value['rate'];
            }
        });

        return $this->selectedServiceCost;
    }

    private function getServiceTitle()
    {
        if (in_array($this->selectedService, $this->uspsServices())) {
            return 'usps';
        }

        if (in_array($this->selectedService, $this->upsServices())) {
            return 'ups';
        }

        if (in_array($this->selectedService, $this->fedexServices())) {
            return 'fedex';
        }
    }

    private function uspsServices()
    {
        return [
            ShippingService::USPS_PRIORITY,
            ShippingService::USPS_FIRSTCLASS,
            ShippingService::USPS_PRIORITY_INTERNATIONAL,
            ShippingService::USPS_FIRSTCLASS_INTERNATIONAL,
        ];
    }

    private function upsServices()
    {
        return [
            ShippingService::UPS_GROUND,
        ];
    }

    private function fedexServices()
    {
        return [
            ShippingService::FEDEX_GROUND,
        ];
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
