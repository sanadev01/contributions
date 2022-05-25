<?php

namespace App\Http\Livewire\AccrualRate;

use Livewire\Component;
use App\Models\ShippingService;
use App\Models\Warehouse\AccrualRate;
use App\Services\Correios\Models\Package;
use App\Repositories\AccrualRateRepository;

class Table extends Component
{
    public $weight;
    public $selectedCountry;
    public $cwb;
    public $gru;
    public $service;
    public $commission;
    public $chileService = false;
    public $anjunService = false;
    
    public function mount($shippingService)
    {
        $this->service = $shippingService;
    }
    
    public function render()
    {
        $this->checkService();

        return view('livewire.accrual-rate.table', [
            'shippingRates' => $this->getAccrualRates(),
        ]);
    }
    private function getAccrualRates()
    {
        return (new AccrualRateRepository)->get(request()->merge([
            'service'=> $this->service,
            'country_id' => $this->selectedCountry,
            'weight' => $this->weight,
            'cwb' => $this->cwb,
            'gru' => $this->gru,
            'commission' => $this->commission,
        ]));
    }

    public function checkService()
    {
        if($this->service == Package::SERVICE_CLASS_SRP || $this->service == Package::SERVICE_CLASS_SRM){
            $this->chileService = true;
        }
        if($this->service == ShippingService::AJ_Packet_Standard || $this->service == ShippingService::AJ_Packet_Express){
            $this->anjunService = true;
        }
    }

}
