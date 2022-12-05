<?php

namespace App\Http\Livewire\GepsContainer;


use App\Models\Order;
use Livewire\Component;
use App\Models\OrderTracking;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Warehouse\GePSContainerPackageController;

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
        $this->service = $container->getServiceSubClass();
        $this->containerDestination = $container->destination_operator_name == 'MIA' ? 'Miami' : '';
    }

    public function render()
    {
        $this->getPackages($this->container->id);
        $this->totalPackages();
        $this->totalWeight();

        return view('livewire.geps-container.packages');
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
        $geps_ContainerPackageController = new GePSContainerPackageController;
        $order = Order::where('corrios_tracking_code', $this->barcode)->first();
            if (!$order) {
                return [
                    'order' => [
                        'corrios_tracking_code' => $this->barcode,
                        'error' => 'Order Not Found.',
                        'code' => 404
                    ],
                ];
            }
            
            if(!$order->containers->isEmpty()) {
    
                $this->error = 'Order is already present in Container'; 
                return $this->barcode = '';
                
            }

            if ($order->status < Order::STATUS_PAYMENT_DONE) {
                return  $this->error = 'Please check the Order Status, either the order has been canceled, refunded or not yet paid';
            }
            if ($this->container->hasGePSService() && !$order->shippingService->isGePSService()) {
                return  $this->error = 'Order does not belong to this container. Please Check Packet Service';
            }
    
            if (!$this->container->hasGePSService() && $order->shippingService->isGePSService()) {
                return  $this->error = 'Order does not belong to this container. Please Check Packet Service';
            }

            $order = $geps_ContainerPackageController->store($this->container, $order);

            $this->addOrderTracking($order);
            $this->error = '';
            return $this->barcode = ''; 
    }

    public function removeOrder($id, $key)
    {
        $geps_ContainerPackageController = new GePSContainerPackageController;

        $geps_ContainerPackageController->destroy($this->container, $id);
        unset($this->orders[$key]);
        
        $this->removeOrderTracking($id);
        $this->error = '';
    }

    public function totalPackages()
    {
        
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
}

