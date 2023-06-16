<?php

namespace App\Http\Livewire\HDExpress;

use Livewire\Component;
use App\Repositories\Warehouse\ContainerRepository;

class Container extends Component
{
    public $packetType='';
    public $dispatchNumber='';
    public $sealNo='';
    public $unitCode='';

    public function render()
    {
        return view('livewire.h-d-express.container', [
            'containers' => $this->getContainers(),
        ]);
    }

    private function getContainers()
    {
        return (new ContainerRepository)->get($this->getRequestData());
    }

    private function getRequestData()
    {
        return request()->merge([
            'dispatchNumber' => $this->dispatchNumber,
            'sealNo' => $this->sealNo,
            'packetType' => $this->packetType,
            'unitCode' => $this->unitCode,
            'typeMileExpress' => true
        ]);
    }
}