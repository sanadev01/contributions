<?php

namespace App\Http\Livewire\GepsContainer;


use App\Models\Order;
use Livewire\Component;
use App\Models\OrderTracking;
use App\Models\Warehouse\Container;
use App\Repositories\Warehouse\GePSContainerPackageRepository;
use App\Http\Controllers\Warehouse\GePSContainerPackageController;

class Packages extends Component
{
    public $container;
    public $orders = [];
    public $editMode;
    public $barcode;
    public $service;
    public $error = '';
    public $num_of_Packages = 0;
    public $totalweight;
    public $containerDestination;
    public $orderRegion;

    public function mount($container = null, $ordersCollection = null, $editMode = null)
    {
        $this->container = $container;
        $this->error = '';
        $this->emit('scanFocus');
        $this->orders = json_decode($ordersCollection);
        $this->editMode = $editMode;
        $this->service = $container->getServiceSubClass();
        $this->containerDestination = $container->destination_operator_name == 'MIA' ? 'Miami' : '';
    }

    public function render()
    {
        //$this->getPackages($this->container->id);
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
            $this->dispatchBrowserEvent('scan-focus');
        }

    }

    public function saveOrder()
    {
        $order = Order::where('corrios_tracking_code', $this->barcode)->first();
            if (!$order) {
                return $this->error = "Order Not Found $this->barcode";
            }

            if(!$order->containers->isEmpty()) {
                return $this->error = "Order is already present in Container $this->barcode";
            }
            
            if ($order['status'] < Order::STATUS_PAYMENT_DONE) {
                return  $this->error = 'Please check the Order Status, either the order has been canceled, refunded or not yet paid';
            }

            if ($this->container->hasGePSService() && !$order->shippingService->isGePSService()) {
                return  $this->error = 'Order does not belong to this container. Please Check Packet Service';
            }
    
            if (!$this->container->hasGePSService() && $order->shippingService->isGePSService()) {
                return  $this->error = 'Order does not belong to this container. Please Check Packet Service';
            }

            $gepsContainerPackageRepository = new GePSContainerPackageRepository;
            $order = $gepsContainerPackageRepository->addOrderToContainer($this->container, $order);

            $this->addOrderTracking($order);
            $this->error = '';
            return $this->barcode = ''; 
    }

    public function removeOrder($id, $key)
    {
        $gepsContainerPackageRepository = new GePSContainerPackageRepository;
        $gepsContainerPackageRepository->removeOrderFromContainer($this->container, $id);
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
}

