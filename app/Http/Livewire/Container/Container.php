<?php

namespace App\Http\Livewire\Container;

use Livewire\Component; 
use App\Repositories\Warehouse\ContainerRepository;
use App\Services\Excel\Export\ContainerExport;

class Container extends Component
{
    public $packetType='';
    public $dispatchNumber='';
    public $sealNo='';
    public $unitCode='';
    public $startDate=null;
    public $endDate=null;
    public $service=null;
    public function render()
    {
        return view('livewire.container.container',[
            'containers' => $this->getContainers()
        ]); 
    }
    public function getContainers()
    {  
        return (new ContainerRepository)->get($this->getRequestData());
    }
    public function download()
    {  
        $export = new ContainerExport($this->getContainers()); 
         $export->handle();
        return $export->download();
    }

    public function getRequestData()
    {
        return request()->merge([
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'service' => $this->service,
            'dispatchNumber' => $this->dispatchNumber,
            'sealNo' => $this->sealNo,
            'packetType' => $this->packetType,
            'unitCode' => $this->unitCode,
        ]);
    }
}
