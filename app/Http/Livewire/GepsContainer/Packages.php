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
    public $idContainer;
    public $orders;
    public $editMode;
    public $barcode;
    public $service;
    public $error = '';
    public $num_of_Packages = 0;
    public $totalweight;
    public $containerDestination;
    // public $orderRegion;

    public function mount($id = null, $editMode = null)
    {
        $this->container = Container::find($id);
        $this->idContainer = $id;
        $this->error = '';
        // $this->emit('scanFocus');        
        // $this->orders = $this->container->orders;
        $this->editMode = $editMode;
        $this->service = $this->container->getServiceSubClass();
        $this->containerDestination = $this->container->destination_operator_name == 'MIA' ? 'Miami' : '';
    }

    public function render()
    {
        return view('livewire.geps-container.packages',[
            'orders' => $this->getPackages($this->idContainer),
            'totalweight' => $this->totalWeight(),
            'num_of_Packages' => $this->totalPackages()
        ]);
    }

    public function getPackages($id)
    {
        $container = Container::find($id);
        $this->container = $container;
        return $this->orders = $container->orders;        
    }

    public function updatedbarcode($barcode)
    {
        $error = null;
        if ( $barcode != null || $barcode != '' ||  strlen($barcode) > 4 ){
            
            $gepsContainerPackageRepository = new GePSContainerPackageRepository;
            $order = $gepsContainerPackageRepository->addOrderToContainer($this->idContainer, $barcode);
            // $this->addOrderTracking($order);
            
        }

    }

    // public function saveOrder()
    // {
    //     if ( $barcode != null || $barcode != '' ||  strlen($barcode) > 4 ){
    //         $this->error = '';
    //         $order = Order::where('corrios_tracking_code', $this->barcode)->first();
    //         if($order->containers->isEmpty()) {
    //             $this->saveOrder();
    //         }else{
    //             $this->barcode = '';
    //             return $this->error = "Order is already present in Container $this->barcode";
    //         }

    //         // $this->dispatchBrowserEvent('scan-focus');
    //     }
    //     $this->error = '';
    //     // $this->getPackages($this->idContainer);
    //     $container = Container::find($this->idContainer);

    //     $order = Order::where('corrios_tracking_code', $this->barcode)->first();
        
    //     if (!$order) {
    //         $this->barcode = ''; 
    //         return $this->error = "Order Not Found $this->barcode";
    //     }
    //     $containerOrder = $container->orders->where('corrios_tracking_code',$this->barcode)->first();
        
    //     if(!$order->containers->isEmpty()) {
    //         $this->barcode = ''; 
    //         return $this->error = "Order is already present in another Container $this->barcode";
    //     }

    //     if ($order->status != Order::STATUS_PAYMENT_DONE) {
    //         $this->barcode = ''; 
    //         return $this->error = 'Please check the Order Status, either the order has been canceled, refunded or not yet paid';
    //     }

    //     if ($container->hasGePSService() && !$order->shippingService->isGePSService()) {
    //         $this->barcode = ''; 
    //         return $this->error = 'Order does not belong to this container. Please Check Packet Service';
    //     }

    //     if (!$container->hasGePSService() && $order->shippingService->isGePSService()) {
    //         $this->barcode = ''; 
    //         return $this->error = 'Order does not belong to this container. Please Check Packet Service';
    //     }

    //     if(!$this->error || $this->error = ''){
    //         $gepsContainerPackageRepository = new GePSContainerPackageRepository;
    //         $order = $gepsContainerPackageRepository->addOrderToContainer($container, $order);
    //         $this->addOrderTracking($order);
    //     }

    //     $this->error = '';
    //     return $this->barcode = ''; 
    // }

    public function removeOrder($id)
    {
        $gepsContainerPackageRepository = new GePSContainerPackageRepository;
        $response = $gepsContainerPackageRepository->removeOrderFromContainer($this->container, $id);
        $this->container->refresh();
        $this->error = '';
    }

    public function totalPackages()
    {
        return  $this->num_of_Packages = count($this->orders);
    }
      
    public function totalWeight()
    {
        return $this->totalweight = $this->orders->sum('weight');
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

