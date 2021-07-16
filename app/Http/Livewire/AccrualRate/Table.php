<?php

namespace App\Http\Livewire\AccrualRate;

use Livewire\Component;
use App\Models\Warehouse\AccrualRate;

class Table extends Component
{
    public $shippingRates;
    public $weight;
    public $selectedCountry;
    public $cwb;
    public $gru;
    public $service;
    
    public function mount($shippingService)
    {
        $this->service = $shippingService;
    }
    
    public function render()
    {
        $this->search();

        return view('livewire.accrual-rate.table', [
            'shippingRates' => $this->shippingRates,
        ]);
    }

    public function search()
    {
        if($this->selectedCountry && !$this->weight && !$this->cwb && !$this->gru)
        {
            $this->searchByCountry();

        }elseif($this->weight && !$this->selectedCountry && !$this->cwb && !$this->gru)
        {
            $this->searchByWeight();

        }elseif($this->cwb)
        {
            $this->searchByCwb();

        }elseif($this->gru)
        {

            $this->searchByGru();

        }elseif($this->selectedCountry && $this->weight && !$this->cwb && !$this->gru)
        {
            $this->searchByCountryAndWeight();
        }
        else {
            $this->getShippingRates();
        }
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

    public function searchByCwb()
    {
        $this->shippingRates = AccrualRate::where('service', $this->service)->where('cwb', 'LIKE', "%{$this->cwb}%")->get();
    }

    public function searchByGru()
    {
        $this->shippingRates = AccrualRate::where('service', $this->service)->where('gru', 'LIKE', "%{$this->gru}%")->get();
    }

    public function searchByCountryAndWeight()
    {
        $this->shippingRates = AccrualRate::where('service', $this->service)->where('country_id', $this->selectedCountry)->where('weight', 'LIKE', "%{$this->weight}%")->get();
    }

}
