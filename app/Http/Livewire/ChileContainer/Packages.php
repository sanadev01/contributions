<?php

namespace App\Http\Livewire\ChileContainer;

use App\Models\Order;
use Livewire\Component;
use App\Models\Warehouse\Container;

use function GuzzleHttp\json_decode;
use App\Http\Controllers\Warehouse\ChileContainerPackageController;

class Packages extends Component
{
    public $container;
    public $orders = [];
    public $editMode;
    public $barcode;
    public $service;
    public $error;
    public $num_of_Packages = 0;
    public $totalweight;

    public function mount($container = null, $ordersCollection = null, $editMode = null)
    {
        $this->container = $container;
        $this->orders = json_decode($ordersCollection);
        $this->editMode = $editMode;

        // condition to check service type
        if($container->services_subclass_code == 'SRM')
        {
            $this->service = 'SRM';
        } else {
            $this->service = 'SRP';
        }
    }

    public function render()
    {
        $this->getPackages($this->container->id);
        $this->totalPackages();
        $this->totalWeight();

        return view('livewire.chile-container.packages');
    }
    public function getPackages($id)
    {
        $container = Container::find($id);
        $ordersCollection = json_encode($container->getOrdersCollections());
        return $this->orders = json_decode($ordersCollection);
        
    }
    public function updatedbarcode($barcode)
    {
        if ( $barcode != null || $barcode != '' ||  strlen($barcode) > 4 ){
            $this->saveOrder();
        }

    }

    public function saveOrder()
    {
        $chile_ContainerPackageController = new ChileContainerPackageController;

        $order = Order::where('corrios_tracking_code', $this->barcode)->where('shipping_service_name' , $this->service)->first();
        
        if($order == null) {
            $this->error = 'Order Not Found against this tracking code. Please Check Packet Service';
            return $this->barcode = '';

        } elseif(!$order->containers->isEmpty()) {

            $this->error = 'Order Already in Container'; 
            return $this->barcode = '';
            
        } else {
            $order = $chile_ContainerPackageController->store($this->container, $order);

            $this->error = '';
            return $this->barcode = '';
        }
        
    }

    public function removeOrder($id, $key)
    {
        $chile_ContainerPackageController = new ChileContainerPackageController;

        $chile_ContainerPackageController->destroy($this->container, $id);
        unset($this->orders[$key]);
        
        $this->error = '';
    }
    
    public function totalPackages(){
        
      return  $this->num_of_Packages = count($this->orders);
    }
    
    public function totalWeight()
    {
        $weight = 0;
        foreach ($this->orders as $order) {
            $weight += $order->weight; 
        }

        return $this->totalweight = $weight;
    }
}
