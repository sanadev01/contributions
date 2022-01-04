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
    public function render(ContainerRepository $containerRepository)
    {
        if (! $this->query) {
            $this->query = $this->getQuery();
        }
        return view('livewire.container.container',[
            'containers'=> $this->query->paginate(50)
        ]);
    }

    public function getQuery()
    {
        $query = Contain::query();
        if ($this->sealNo) {
         $query->where('seal_no', 'LIKE', '%' . $this->sealNo . '%');
        }
        if($this->dispatchNumber){
            $query->where('dispatch_number', 'LIKE', '%' . $this->dispatchNumber . '%');
        }
        if($this->packetType){
            $query->where('services_subclass_code', 'LIKE', '%' . $this->packetType . '%');
        }
        return $query;
    }
}
