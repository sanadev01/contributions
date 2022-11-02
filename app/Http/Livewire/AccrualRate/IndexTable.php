<?php

namespace App\Http\Livewire\AccrualRate;

use Livewire\Component;
use App\Services\Correios\Models\Package;

class IndexTable extends Component
{
    protected $services;

    public function render()
    {
        return view('livewire.accrual-rate.index-table', [
            'services' => $this->getServices(),
        ]);
    }

    public function getServices()
    {
        $this->services[0] = [
            'name' => 'Standard',
            'value' => Package::SERVICE_CLASS_STANDARD,
        ];
        $this->services[1] = [
            'name' => 'Express',
            'value' => Package::SERVICE_CLASS_EXPRESS,
        ];
        $this->services[2] = [
            'name' => 'Mini',
            'value' => Package::SERVICE_CLASS_MINI,
        ];
        $this->services[3] = [
            'name' => 'SRP',
            'value' => Package::SERVICE_CLASS_SRP,
        ];
        $this->services[4] = [
            'name' => 'SRM',
            'value' => Package::SERVICE_CLASS_SRM,
        ];
        $this->services[5] = [
            'name' => 'Anjun Standard',
            'value' => Package::SERVICE_CLASS_AJ_Standard,
        ];
        $this->services[6] = [
            'name' => 'Anjun Express',
            'value' => Package::SERVICE_CLASS_AJ_EXPRESS,
        ];
        $this->services[7] = [
            'name' => 'GePS',
            'value' => Package::SERVICE_CLASS_GePS,
        ];
        return $this->services;
    }
}
