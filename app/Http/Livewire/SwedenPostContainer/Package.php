<?php

namespace App\Http\Livewire\SwedenPostContainer;

use App\Models\Order;
use Livewire\Component;
use App\Models\OrderTracking;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Warehouse\SwedenPostContainerPackageController;
use App\Repositories\Warehouse\SwedenPostContainerPackageRepository;
use App\Services\SwedenPost\Services\Container\DirectLinkReceptacle;
use Illuminate\Support\Facades\DB;

class Package extends Component
{
    // public $container;
    // public $orders = [];
    // public $editMode;
    // public $barcode;
    // public $service;
    // public $error;
    // public $num_of_Packages = 0;
    // public $totalweight;
    // public $containerDestination;
    // public $orderRegion;


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
        return view('livewire.sweden-post-container.package', [
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







        //new code 

        $this->validate();
        $order = Order::where('corrios_tracking_code', $this->tracking)->first();
        if ($order) {
            $container = Container::find($this->idContainer);
            // $gepsContainerPackageRepository = new GePSContainerPackageRepository;
            // $response = $gepsContainerPackageRepository->addOrderToContainer($container, $order);


            $swedenpost_containerPackageRepository = new SwedenPostContainerPackageRepository();

            $response =  $swedenpost_containerPackageRepository->addOrderToPackageContainer($container, $order);


            if (!$response['success']) {
                return $this->error = $response['message'];
            }
            $this->error = null;
            return;
        }
        $this->error = "Order Not found please check tracking code: $this->tracking";
    }

    public function removeOrder(Order $order)
    {
        $swedenpost_containerPackageRepository = new SwedenPostContainerPackageRepository();
        return $swedenpost_containerPackageRepository->removeOrderFromPackageContainer($this->container, $order->id);
    }

    public function totalPackages()
    {
        return  $this->num_of_Packages = count($this->orders);
    }

    public function totalWeight()
    {
        $orders = $this->container->orders();
        return $orders->selectRaw('sum(CASE WHEN measurement_unit = "kg/cm" THEN ROUND(weight,2) ELSE ROUND((weight/2.205),2) END) as weight')->first()->weight;
    }



    // public function mount($container = null, $ordersCollection = null, $editMode = null)
    // {
    //     $this->container = $container;
    //     $this->emit('scanFocus');
    //     $this->orders = json_decode($ordersCollection);
    //     $this->editMode = $editMode;
    //     $this->service = $container->getServiceSubClass();
    //     $this->containerDestination = $container->destination_operator_name == 'MIA' ? 'Miami' : '';
    // }

    // public function render()
    // {
    //     $this->getPackages($this->container->id);
    //     $this->totalPackages();
    //     $this->totalWeight();

    //     return view('livewire.sweden-post-container.package');
    // }

    // public function getPackages($id)
    // {
    //     $container = Container::find($id);
    //     $ordersCollection = json_encode($container->getOrdersCollections());
    //     return $this->orders = json_decode($ordersCollection);

    // }

    // public function updatedbarcode($barcode)
    // {
    //     if ( $barcode != null || $barcode != '' ||  strlen($barcode) > 4 ){
    //         $this->saveOrder();
    //         $this->dispatchBrowserEvent('scan-focus');
    //     }

    // }

    // public function saveOrder()
    // {
    //     $this->error = '';
    //     DB::beginTransaction();

    //     $swedenpost_ContainerPackageController = new SwedenPostContainerPackageController;
    //     $order = Order::where('corrios_tracking_code', $this->barcode)->first();
    //         if (!$order) {
    //             return [
    //                 'order' => [
    //                     'corrios_tracking_code' => $this->barcode,
    //                     'error' => 'Order Not Found.',
    //                     'code' => 404
    //                 ],
    //             ];
    //         }

    //         if(!$order->containers->isEmpty()) {

    //             $this->error = "Order is already present in Container $this->barcode"; 
    //             return $this->barcode = '';

    //         }

    //         if ($order->status < Order::STATUS_PAYMENT_DONE) {
    //             return  $this->error = 'Please check the Order Status, either the order has been canceled, refunded or not yet paid';
    //         }
    //         if ($this->container->hasGePSService() && !$order->shippingService->isGePSService()) {
    //             return  $this->error = 'Order does not belong to this container. Please Check Packet Service';
    //         }

    //         if (!$this->container->hasGePSService() && $order->shippingService->isGePSService()) {
    //             return  $this->error = 'Order does not belong to this container. Please Check Packet Service';
    //         }
    //         $response =  (new DirectLinkReceptacle($this->container))->scanItem($this->barcode);
    //         $data = $response->getData(); 
    //         if ($data->isSuccess) {
    //                 $order = $swedenpost_ContainerPackageController->store($this->container, $order); 
    //                 $this->addOrderTracking($order); 
    //                 DB::commit();
    //                 $this->error = $data->message;
    //                 return $this->barcode = ''; 

    //         } else {
    //             DB::rollback();
    //             return  $this->error = $data->message;
    //             return null;
    //         }

    // }

    // public function removeOrder(Order $order, $key)
    // { 
    //     $this->error = ''; 
    //     $response =  (new DirectLinkReceptacle($this->container))->removeItem($order->corrios_tracking_code);
    //     $data = $response->getData();
    //     //  return dd($data);
    //     if ($data->isSuccess) { 
    //            $this->error =  $data->message; 
    //             $swedenpost_ContainerPackageController = new SwedenPostContainerPackageController;
    //             $swedenpost_ContainerPackageController->destroy($this->container, $order->id); 
    //             DB::commit(); 

    //     } else {
    //         DB::rollback();
    //         return  $this->error = $data->message;  
    //     }

    // }

    // public function totalPackages()
    // {

    //     return  $this->num_of_Packages = count($this->orders);
    // }

    // public function totalWeight()
    // {
    //     $weight = 0;
    //     foreach ($this->orders as $order) {
    //         $weight += $order->weight; 
    //     }

    //     return $this->totalweight = $weight;
    // }

    // public function addOrderTracking($order)
    // {
    //     OrderTracking::create([
    //         'order_id' => $order->id,
    //         'status_code' => Order::STATUS_INSIDE_CONTAINER,
    //         'type' => 'HD',
    //         'description' => 'Parcel inside Homedelivery Container',
    //         'country' => 'US',
    //         'city' => 'Miami'
    //     ]);

    //     return true;
    // }
}
