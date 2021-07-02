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
    public $containerDestination;
    public $orderRegion;

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
        
        if($container->destination_operator_name == 'MR')
        {
            $this->containerDestination = 'Santiago';     
        }else{
            $this->containerDestination = 'notSantiago';
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
        
        if($order != null)
        {
           
            if($order->recipient->region != '214')   //214 is Code of Santigao Region defined by Correos Chile
            {
                $this->orderRegion = 'notSantiago';
            }else{
                $this->orderRegion = 'Santiago';
            }


            if($this->orderRegion != $this->containerDestination) {
                
                $this->error = 'Order does not belong to this container, please check packet destination';
                return $this->barcode = '';
    
            }
            
            
            if(!$order->containers->isEmpty()) {
    
                $this->error = 'Order is already present in Container'; 
                return $this->barcode = '';
                
            }

            $order = $chile_ContainerPackageController->store($this->container, $order);

            $this->error = '';
            return $this->barcode = '';
            
        }

        $this->error = 'Order does not belong to this container. Please Check Packet Service';
        return $this->barcode = '';
        
        
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
