<?php

namespace App\Http\Livewire\AccrualRate;

use Livewire\Component;
use App\Models\Warehouse\AccrualRate;

class Table extends Component
{
    public $shippingRates;
    public $weight;
    public $selectedCountry;
    public $service;
    
    public function mount($shippingService)
    {
        $this->service = $shippingService;
    }
    
    public function render()
    {
        if($this->selectedCountry && !$this->weight)
        {
            $this->searchByCountry();
        }elseif(!$this->selectedCountry && $this->weight)
        {
            $this->searchByWeight();
        }elseif($this->selectedCountry && $this->weight)
        {
            $this->searchByCountryAndWeight();
        }
        else {
            $this->getShippingRates();
        }

        return view('livewire.accrual-rate.table', [
            'shippingRates' => $this->shippingRates,
        ]);
    }

    public function getShippingRates()
    {
        
        return $this->shippingRates = AccrualRate::where('service', $this->service)->get();
    }

    public function searchByCountry()
    {
        $this->shippingRates = AccrualRate::where('service', $this->service)->where('country_id', $this->selectedCountry)->get();
    }

    public function searchByWeight()
    {
        $this->shippingRates = AccrualRate::where('service', $this->service)->where('weight', 'LIKE', "%{$this->weight}%")->get();
    }

    public function searchByCountryAndWeight()
    {
        $this->shippingRates = AccrualRate::where('service', $this->service)->where('country_id', $this->selectedCountry)->where('weight', 'LIKE', "%{$this->weight}%")->get();
    }

}
