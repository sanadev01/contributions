<?php

namespace App\Http\Livewire\ColombiaContainer;

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
            'containers' => $this->getContainers($paginate = true)
        ]); 
    }
    public function getContainers($paginate = true)
    {  
        return (new ContainerRepository)->get($this->getRequestData(), $paginate);
    }
    public function download()
    {  
        $export = new ContainerExport($this->getContainers($paginate = false)); 
         $export->handle();
        return $export->download();
    }

    private function getRequestData()
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
