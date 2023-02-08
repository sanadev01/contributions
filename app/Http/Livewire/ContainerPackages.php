<?php

namespace App\Http\Livewire;

use App\Models\Order;
use Livewire\Component;
use App\Models\OrderTracking;
use App\Models\Region;
use App\Models\ShippingService;
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
    public $shippingServiceCodes;
    public $santiagoRegionCode;

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

        if($this->containerService == 'Chile-Container'){
            $this->containerDestination = ($container->destination_operator_name == 'MR') ? 'Santiago' : 'notSantiago';
            $this->santiagoRegionCode = Region::REGION_SANTIAGO;
        }

        $this->setShippingServiceCodes();
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
        $this->error = '';
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

            if ($this->containerService == 'Chile-Container' && !$this->validateOrderRegion($order)) {
                $this->error = 'Order does not belong to this container, please check packet destination';
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

        if($this->containerService == 'PostNL' && $order->shippingService->isPostNLService()){
            return true;
        }

        if($this->containerService == 'GePS' && $order->shippingService->isGePSService()){
            return true;
        }

        if($this->containerService == 'Anjun-Container' && $order->shippingService->isAnjunService()){
            return true;
        }

        if($this->containerService == 'USPS-Container' && $order->shippingService->isUSPSService()){

            if($this->service == 'Priority International' && $order->shippingService->service_sub_class != $this->shippingServiceCodes['USPS_PRIORITY_INTERNATIONAL']) {
                return false;
            }

            if($this->service == 'FirstClass International' && $order->shippingService->service_sub_class != $this->shippingServiceCodes['USPS_FIRSTCLASS_INTERNATIONAL']) {
                return false;
            }

            if($this->service == 'Priority' && $order->shippingService->service_sub_class != $this->shippingServiceCodes['USPS_PRIORITY']) {
                return false;
            }

            if($this->service == 'FirstClass' && $order->shippingService->service_sub_class != $this->shippingServiceCodes['USPS_FIRSTCLASS']) {
                return false;
            }

            return true;
        }

        if($this->containerService == 'Chile-Container' && $order->shippingService->isCorreiosChileService()){

            if ($this->service == 'SRP' &&  $order->shippingService->service_sub_class != $this->shippingServiceCodes['SRP']) {
                return false;
            }

            if ($this->service == 'SRM' &&  $order->shippingService->service_sub_class != $this->shippingServiceCodes['SRM']) {
                return false;
            }

            return true;
        }

        if($this->containerService == 'Brazil-Container' && $order->shippingService->isCorreiosBrazilService()){
            return true;
        }

        if($this->containerService == 'MileExpress-Container' && $order->shippingService->isMileExpressService()){
            return true;
        }

        return false;
    }

    private function validateOrderRegion($order)
    {
        if ($order->recipient->region != $this->santiagoRegionCode) {
            $orderRegion = 'notSantiago';
        }else{
            $orderRegion = 'Santiago';
        }

        if ($orderRegion != $this->containerDestination) {
            return false;
        }

        return true;
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

    private function setShippingServiceCodes()
    {
        $this->shippingServiceCodes = [
            'USPS_PRIORITY' => ShippingService::USPS_PRIORITY,
            'USPS_FIRSTCLASS' => ShippingService::USPS_FIRSTCLASS,
            'USPS_PRIORITY_INTERNATIONAL' => ShippingService::USPS_PRIORITY_INTERNATIONAL,
            'USPS_FIRSTCLASS_INTERNATIONAL' => ShippingService::USPS_FIRSTCLASS_INTERNATIONAL,
            'SRP' => ShippingService::SRP,
            'SRM' => ShippingService::SRM,
            'Courier_Express' => ShippingService::Courier_Express,
            'UPS_GROUND' => ShippingService::UPS_GROUND,
            'FEDEX_GROUND' => ShippingService::FEDEX_GROUND,
            'PostNL' => ShippingService::PostNL,
            'GePS' => ShippingService::GePS,
        ];

        return;
    }
}
