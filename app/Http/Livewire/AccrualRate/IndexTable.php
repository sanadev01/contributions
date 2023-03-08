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
            'services' => collect($this->getServices())->sortBy('name')->toArray(),
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
            'name' => 'Global eParcel Prime',
            'value' => Package::SERVICE_CLASS_GePS,
        ];
        $this->services[8] = [
            'name' => 'Global eParcel Untracked Packet',
            'value' => Package::SERVICE_CLASS_GePS_EFormat,
        ];
        $this->services[9] = [
            'name' => 'Prime5',
            'value' => Package::SERVICE_CLASS_Prime5,
        ];
        $this->services[10] = [
            'name' => 'Post Plus Registered',
            'value' => Package::SERVICE_CLASS_Post_Plus_Registered,
        ];
        $this->services[11] = [
            'name' => 'Post Plus EMS',
            'value' => Package::SERVICE_CLASS_Post_Plus_EMS,
        ];
        $this->services[12] = [
            'name' => 'Parcel Post',
            'value' => Package::SERVICE_CLASS_Parcel_Post,
        ];
        return $this->services;
    }
}
