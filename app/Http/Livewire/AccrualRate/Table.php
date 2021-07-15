<?php

namespace App\Http\Livewire\AccrualRate;

use Livewire\Component;
use App\Models\Warehouse\AccrualRate;

class Table extends Component
{
    public $shippingRates;
    public $selectedService;
    public $weight;
    public $selectedCountry;
    
    
    public function render()
    {
        //Filter by country
        if($this->selectedCountry && !$this->selectedService && !$this->weight)
        {
            $this->shippingRates = $this->getShippingRatesByCountry();
        }   
        //Filter by Service
        elseif($this->selectedService && !$this->weight && !$this->selectedCountry)
        {
            $this->shippingRates = $this->getShippingRatesByServiceName();
        }
        //Filter by Weight
        elseif($this->weight && !$this->selectedService && !$this->selectedCountry)
        {
            $this->shippingRates = $this->getShippingRatesByWeight();
        }
        //Filter by Country and Service
        elseif($this->selectedCountry && $this->selectedService && !$this->weight)
        {
            $this->shippingRates = $this->getShippingRatesByServiceName_and_ByCountry();
        }
        //Filter by Weight and Service
        elseif($this->selectedService && $this->weight && !$this->selectedCountry)
        {
            $this->shippingRates = $this->getShippingRatesByServiceName_and_ByWeight();
        }
        // Filter by Country Weight and Service
        elseif($this->selectedCountry && $this->weight && $this->selectedService)
        {
            $this->shippingRates = $this->getShippingRatesByCountry_and_ByService_and_ByWeight();
        }
        else{
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

    public function getShippingRatesByCountry()
    {
        return AccrualRate::where('country_id', $this->selectedCountry)->get();
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

    public function getShippingRatesByServiceName_and_ByCountry()
    {
        return AccrualRate::where('service', $this->selectedService)->where('country_id', $this->selectedCountry)->get();
    }

    public function getShippingRatesByCountry_and_ByService_and_ByWeight()
    {
        return AccrualRate::where('country_id', $this->selectedCountry)->where('service', $this->selectedService)->where('weight', 'LIKE', "%{$this->weight}%")->get();
    }
}
