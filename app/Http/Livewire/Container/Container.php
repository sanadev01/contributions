<?php

namespace App\Http\Livewire\Container;

use Livewire\Component;
use App\Models\Warehouse\Container as Contain;
use App\Repositories\Warehouse\ContainerRepository;
class Container extends Component
{
    private $query;
    public $packetType='';
    public $dispatchNumber='';
    public $sealNo='';
    public $search='';
    public $unitCode='';
    public function render(ContainerRepository $containerRepository)
    {
        return view('livewire.container.container',[
            'containers'=>  $this->getContainers()
        ]);
    }
    public function getContainers()
    {
        return (new ContainerRepository)->get($this->getRequestData());
    }

    public function getRequestData()
    {
        return request()->merge([
            'dispatchNumber' => $this->dispatchNumber,
            'sealNo' => $this->sealNo,
            'packetType' => $this->packetType,
            'search' => $this->search,
            'unitCode' => $this->unitCode
        ]);
    }
}
