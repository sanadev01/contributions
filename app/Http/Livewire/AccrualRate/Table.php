<?php

namespace App\Http\Livewire\AccrualRate;

use Livewire\Component;
use App\Models\Warehouse\AccrualRate;
use App\Services\Correios\Models\Package;

class Table extends Component
{
    public $shippingRates;
    public $weight;
    public $selectedCountry;
    public $cwb;
    public $gru;
    public $service;
    public $chileService = false;
    public $search;
    
    public function mount($shippingService)
    {
        $this->service = $shippingService;
    }
    
    public function render()
    {
        $this->checkService();
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
        elseif($this->search)
        {
            return $this->shippingRates = AccrualRate::where('gru', 'LIKE', "%{$this->search}%")
            ->orWhere('weight', 'LIKE', "%{$this->search}%")
            ->orWhere('cwb', 'LIKE', "%{$this->search}%")->get();
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

    public function checkService()
    {
        if($this->service == Package::SERVICE_CLASS_SRP || $this->service == Package::SERVICE_CLASS_SRM)
        {
            $this->chileService = true;
        }
    }

}
