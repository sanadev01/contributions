<?php

namespace App\Http\Livewire\AccrualRate;

use Livewire\Component;
use App\Models\Warehouse\AccrualRate;

class Table extends Component
{
    public $shippingRates;
    public $selectedService;
    public $weight;
    
    
    public function render()
    {
        if($this->selectedService && !$this->weight)
        {
            $this->shippingRates = $this->getShippingRatesByServiceName();

        }elseif($this->weight && !$this->selectedService)
        {
            $this->shippingRates = $this->getShippingRatesByWeight();

        }elseif($this->selectedService && $this->weight)
        {
            $this->shippingRates = $this->getShippingRatesByServiceName_and_ByWeight();
        }else {

            $this->shippingRates = $this->getAllShippingRates();
        }

        return view('livewire.accrual-rate.table', [
            'shippingRates' => $this->shippingRates,
        ]);
    }

    public function getAllShippingRates()
    {
        return AccrualRate::all();
    }

    public function getShippingRatesByServiceName()
    {
        return AccrualRate::where('service', $this->selectedService)->get();
    }

    public function getShippingRatesByWeight()
    {
        return AccrualRate::where('weight', 'LIKE', "%{$this->weight}%")->get();
    }

    public function getShippingRatesByServiceName_and_ByWeight()
    {
        return AccrualRate::where('service', $this->selectedService)->where('weight', 'LIKE', "%{$this->weight}%")->get();
    }
}
