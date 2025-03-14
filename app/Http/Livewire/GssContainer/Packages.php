<?php

namespace App\Http\Livewire\GssContainer;

use App\Models\Order;
use Livewire\Component;
use App\Models\OrderTracking;
use App\Models\Warehouse\Container;
use App\Repositories\Warehouse\GSSContainerPackageRepository;
use App\Http\Controllers\Warehouse\GSSContainerPackageController;

class Packages extends Component
{
    public $container;
    public $idContainer;
    public $orders;
    public $editMode;
    public $tracking;
    public $error = null;
    public $num_of_Packages = 0;
    protected $rules = [
        'tracking' => 'required',
    ];
    public function mount($id = null, $editMode = null)
    {
        $this->container = Container::find($id);
        $this->idContainer = $id;
        $this->error = null;
        $this->emit('scanFocus');
        $this->editMode = $editMode;
    }

    public function render()
    {
        $this->dispatchBrowserEvent('scan-focus');
        $this->tracking = null;
        return view('livewire.gss-container.packages',[
            'orders' => $this->getPackages($this->idContainer),
            'totalweight' => $this->totalWeight(),
            'num_of_Packages' => $this->totalPackages()
        ]);
    }

    public function getPackages($id)
    {
        $container = Container::find($id);
        return $this->orders = $container->orders;        
    }

    public function submit()
    {
        $this->validate();
        $order = Order::where('corrios_tracking_code', $this->tracking)->first();
        if ($order){
            $container = Container::find($this->idContainer);
            $gssContainerPackageRepository = new GSSContainerPackageRepository;
            $response = $gssContainerPackageRepository->addOrderToContainer($container, $order);
            if(!$response['success']){
                return $this->error = $response['message'];
            }
            $this->error = null;
            return;
        }
        $this->error = "Order Not found please check tracking code: $this->tracking";
    }

    public function removeOrder($id)
    {
        $gssContainerPackageRepository = new GSSContainerPackageRepository;
        $response = $gssContainerPackageRepository->removeOrderFromContainer($this->container, $id);
        $this->error = null;
    }

    public function totalPackages()
    {
        return  $this->num_of_Packages = count($this->orders);
    }
      
    public function totalWeight()
    {
        $orders = $this->container->orders()->get();

        $totalWeight = $orders->sum(function ($order) {
            $measurementUnit = is_string($order->measurement_unit) ? $order->measurement_unit : '';
            return round($measurementUnit === 'kg/cm' ? $order->weight * 2.205 : $order->weight, 2);
        });
        
        return $totalWeight;
        
    }
}
