<?php

namespace App\Http\Livewire\ChileContainer;

use App\Models\Order;
use Livewire\Component;
use App\Models\OrderTracking;

use App\Models\Warehouse\Container;
use function GuzzleHttp\json_decode;
use App\Http\Controllers\Warehouse\ChileContainerPackageController;
use App\Models\ShippingService;

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

        $this->checkContainerShippingService($container->services_subclass_code);
        
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
            $this->dispatchBrowserEvent('focus-barcode');
        }

    }

    public function saveOrder()
    {
        $chile_ContainerPackageController = new ChileContainerPackageController;
        
        $tracking_number = strlen($this->barcode) > 12 ? substr(substr($this->barcode, 11), 0, -3) : $this->barcode;

        $order = Order::where('corrios_tracking_code', $tracking_number)->where('shipping_service_name' , $this->service)->first();

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

            $this->addOrderTracking($order);
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
        
        $this->removeOrderTracking($id);
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

    public function addOrderTracking($order)
    {
        OrderTracking::create([
            'order_id' => $order->id,
            'status_code' => Order::STATUS_INSIDE_CONTAINER,
            'type' => 'HD',
            'description' => 'Parcel inside Homedelivery Container',
            'country' => 'US',
            'city' => 'Miami'
        ]);

        return true;
    }

    public function removeOrderTracking($id)
    {

        $order_tracking = OrderTracking::where('order_id', $id)->latest()->first();

        $order_tracking->delete();

        return true;

    }

    private function checkContainerShippingService($container_service)
    {
        
        if($container_service == 'SRM')
        {
            $shipping_service = ShippingService::where('service_sub_class', ShippingService::SRM)->first();
            $this->service = $shipping_service->name;
            return;
        }
        
        $shipping_service = ShippingService::where('service_sub_class', ShippingService::SRP)->first();
        $this->service = $shipping_service->name;
        return;
    }
         
}
