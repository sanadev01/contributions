<?php

namespace App\Http\Livewire\Calculator;

use Livewire\Component;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use App\Repositories\Calculator\USCalculatorRepository;
use Illuminate\Support\Facades\Auth;

class UsCalculatorRates extends Component
{
    public $apiRates;
    public $ratesWithProfit;
    public $tempOrder;
    public $weightInOtherUnit;
    public $chargableWeight;
    public $userLoggedIn;
    public $shippingServiceTitle;
    public $isInternational;
    public $serviceResponse = false;

    public $serviceError;
    public $selectedService;
    private $selectedServiceCost;
    public $selectedTaxModality;
    public $type;
    public $listeners = ['acceptedAndContinue'];

    public function updatedSelectedTaxModality($value)
    {
        $this->tempOrder['tax_modality']  = strtolower($value) == "ddp" ? 'ddp' : 'ddu';
    }
    public function mount($apiRates, $ratesWithProfit, $tempOrder, $weightInOtherUnit, $chargableWeight, $userLoggedIn, $shippingServiceTitle, $isInternational)
    {
        $this->apiRates = $apiRates;
        $this->ratesWithProfit = $ratesWithProfit;
        $this->tempOrder = $tempOrder->toArray();
        $this->weightInOtherUnit = $weightInOtherUnit;
        $this->chargableWeight = $chargableWeight;
        $this->userLoggedIn = $userLoggedIn;
        $this->shippingServiceTitle = $shippingServiceTitle;
        $this->isInternational = $isInternational;
        $this->selectedTaxModality = strtolower($this->tempOrder['tax_modality']) == "ddp" ? 'ddp' : 'ddu';
    }

    public function render()
    {
        return view('livewire.calculator.us-calculator-rates');
    }

    public function getLabel()
    {
        $usCalculatorRepository = new  USCalculatorRepository();
        if (!$this->selectedService) {
            $this->addError('selectedService', 'select service please.');
            $this->dispatchBrowserEvent('fadeOutLoading');
            return false;
        }

        if (!$this->userLoggedIn) {
            $this->addError('serviceError', 'Please login to continue.');
            $this->dispatchBrowserEvent('fadeOutLoading');
            return false;
        }

        if ($this->getCostOfSelectedService() > getBalance()) {
            $this->addError('serviceError', 'insufficient balance, please recharge your account.');
            $this->dispatchBrowserEvent('fadeOutLoading');
            return false;
        }

        if ($this->selectedServiceEnabledForUser()) {
            $order = $usCalculatorRepository->executeForLabel($this->createRequest());
            $this->addError('serviceError', $usCalculatorRepository->getError());

            if ($order) {
                return redirect()->route('admin.orders.label.index', $order->id);
            }
            $this->dispatchBrowserEvent('fadeOutLoading');
        }
        $this->dispatchBrowserEvent('fadeOutLoading');
    }
    public function openModel($subClass, $userDeclaredFreight, $type)
    {

        $this->type = $type;
        $this->tempOrder['user_declared_freight'] = $userDeclaredFreight;
        $this->selectedService = $subClass;
        $this->dispatchBrowserEvent('termAndConditionOpen');
    }
    public function acceptedAndContinue()
    {
        if ($this->type == 'lable') {
            return $this->getLabel();
        } else {
            return $this->createOrder();
        }
    }
    public function createOrder()
    {
        $usCalculatorRepository = new  USCalculatorRepository();
        if (!$this->selectedService) {
            $this->addError('selectedService', 'select service please.');
            $this->dispatchBrowserEvent('fadeOutLoading');
            return false;
        }

        if (!$this->userLoggedIn) {
            $this->addError('serviceError', 'Please login to continue.');
            $this->dispatchBrowserEvent('fadeOutLoading');
            return false;
        }

        if ($this->selectedServiceEnabledForUser()) {
            $order = $usCalculatorRepository->executeForPlaceOrder($this->createRequest());
            $this->addError('serviceError', $usCalculatorRepository->getError());

            if ($order) {
                return redirect()->route('admin.orders.sender.index', $order);
            }
        }
        $this->dispatchBrowserEvent('fadeOutLoading');
    }
    public function calculateTotal($serviceSubClass, $profitRate)
    {
        $shippingService = ShippingService::where('service_sub_class',$serviceSubClass)->first();
        
        $isUSPS = optional($shippingService)->usps_service_sub_class ?? false; 
        $userProfit = $this->calculateProfit($profitRate, $serviceSubClass, Auth::id());

        $orderValue = array_reduce($this->tempOrder['items'], function ($carry, $item) {
    
            return $item['value'] * $item['quantity'] + $carry;
        }, 0);
        $totalCost = $profitRate + $orderValue;

        $isPRCUser = setting('is_prc_user', null, Auth::id());
        if ((strtolower($this->selectedTaxModality) == "ddp" || $isPRCUser) && !$isUSPS) {
            $duty = $totalCost > 50 ? $totalCost * .60 : 0;
            $totalCostOfTheProduct = $totalCost + $duty;
            $icms = .17;
            $totalIcms = $icms * $totalCostOfTheProduct;
            $totalTaxAndDuty = $duty + $totalIcms;
            $feeForTaxAndDuty = $this->calculateFeeForTaxAndDuty($totalTaxAndDuty);
        } else {
            $totalTaxAndDuty = 0;
            $feeForTaxAndDuty = 0;
        }

        return number_format($feeForTaxAndDuty + number_format($totalTaxAndDuty, 2) + number_format(+$profitRate, 2) + $userProfit, 2);
    }
    function calculateProfit($shippingCost, $serviceSubClass, $user_id)
    {
        switch ((int)$serviceSubClass) {
            case ShippingService::UPS_GROUND:
                $profit_percentage = setting('ups_profit', null, $user_id) ?? setting('ups_profit', null, User::ROLE_ADMIN);
                break;
            case ShippingService::FEDEX_GROUND:
                $profit_percentage = setting('fedex_profit', null, $user_id) ?? setting('fedex_profit', null, User::ROLE_ADMIN);
                break;
            default:
                $profit_percentage = setting('usps_profit', null, $user_id) ?? setting('usps_profit', null, User::ROLE_ADMIN);
        }
        return number_format(($shippingCost * ($profit_percentage / 100)), 2);
    }
    public function calculateFeeForTaxAndDuty($totalTaxAndDuty)
    {
        $fee = 0;
        if ($totalTaxAndDuty > 0) {
            $flag = true;
            if (setting('prc_user_fee', null, Auth::id()) == "flat_fee") {
                $fee = setting('prc_user_fee_flat', null, Auth::id()) ?? 2;
                $flag = false;
            }
            if (setting('prc_user_fee', null, Auth::id()) == "variable_fee") {
                $percent = setting('prc_user_fee_variable', null, Auth::id()) ?? 1;
                $fee = $totalTaxAndDuty / 100 * $percent;
                $fee = $fee < 0.5 ? 0.5 : $fee;
                $flag = false;
            }
            if ($flag) {
                $fee = $totalTaxAndDuty * .01;
                $fee = $fee < 0.5 ? 0.5 : $fee;
            }
        }
        return $fee;
    }

    public function downloadRates()
    {
        if ($this->ratesWithProfit) {
            $usCalculatorRepository = new USCalculatorRepository();

            return $usCalculatorRepository->download($this->ratesWithProfit, $this->tempOrder, $this->chargableWeight, $this->weightInOtherUnit);
        }
    }
    public function getTaxAndDuty($userDeclaredFreight, $tax_modality)
    {
        if (strtolower($tax_modality) == "ddp") {
            $total = $userDeclaredFreight * 2;
            $tax = $total > 50 ? ($userDeclaredFreight * 0.6) : 0;
            $subTotal = $tax + $total;
            $totalIcms = $subTotal * 0.17;
            $overAllTotal = $subTotal + $totalIcms;
            return $overAllTotal;
        }
        return $userDeclaredFreight;
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
