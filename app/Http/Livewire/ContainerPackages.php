<?php

namespace App\Http\Livewire;

use App\Models\Order;
use Livewire\Component;
use App\Models\OrderTracking;
use App\Models\Warehouse\Container;
use App\Repositories\Warehouse\ContainerRepository;

class ContainerPackages extends Component
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
    public $containerService;
    
    public function mount($container = null, $ordersCollection = null, $editMode = null)
    {
        $this->container = $container;
        $this->orders = json_decode($ordersCollection);
        $this->editMode = $editMode;
        $this->service = $container->services_subclass_code;
        $this->containerService = $this->container->getContainerService();
        $this->containerDestination = $container->destination_operator_name;

        if ($this->containerService == 'USPS-Container') {
            $this->containerDestination = $container->destination_operator_name == 'MIA' ? 'Miami' : '';
        }
    }

    public function render()
    {
        $this->getPackages($this->container->id);
        $this->totalPackages();
        $this->totalWeight();

        return view('livewire.container-packages');
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

    private function saveOrder()
    {
        $order = Order::where('corrios_tracking_code', $this->barcode)->first();
        
        if ($order) {
            
            if ($order->containers->isNotEmpty()) {
                $this->error = 'Order is already present in Container';
                $this->barcode = '';
                return;
            }

            if (!$this->validateOrderService($order)) {
                $this->error = 'Order does not belong to this container. Please Check Packet Service';
                $this->barcode = '';
                return;
            }

            $containerRepository = new ContainerRepository();

            if ($containerRepository->addOrderToContainer($this->container, $order->id)) {
                $this->addOrderTracking($order);
                $this->error = '';
                $this->barcode = '';

                return;
            }

            $this->error = $containerRepository->getError();
            return;
        }

        $this->error = 'Order does not belong to this container. Please Check Packet Service';
        return $this->barcode = '';
    }

    public function removeOrder($id, $key)
    {
        $containerRepository = new ContainerRepository();

        if ($containerRepository->removeOrderFromContainer($this->container, $id)) {
            unset($this->orders[$key]);
            $this->removeOrderTracking($id);
            $this->error = '';

            return;
        }

        $this->error = $containerRepository->getError();
        return;
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

    private function validateOrderService($order)
    {
        if($this->containerService == 'Colombia-Container' && $order->shippingService->isColombiaService()){
            return true;
        }

        if($this->containerService == 'Anjun-Container' && $order->shippingService->isAnjunService()){
            return true;
        }

        if($this->containerService == 'USPS-Container' && $order->shippingService->isUSPSService()){
            return true;
        }

        if($this->containerService == 'Chile-Container' && $order->shippingService->isCorreiosChileService()){
            return true;
        }

        if($this->containerService == 'Brazil-Container' && $order->shippingService->isCorreiosBrazilService()){
            return true;
        }

        return false;
    }

    private function addOrderTracking($order)
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

    private function removeOrderTracking($id)
    {
        $order_tracking = OrderTracking::where('order_id', $id)->latest()->first();

        $order_tracking->delete();

        return true;
    }
}