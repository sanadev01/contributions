<?php

namespace App\Repositories\Warehouse;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\OrderTracking;
use App\Models\Warehouse\Container;
use Illuminate\Support\Facades\Auth;
use App\Repositories\AbstractRepository;
use App\Services\Correios\Services\Brazil\Client;
use App\Http\Resources\Warehouse\Container\PackageResource;

class ContainerPackageRepository extends AbstractRepository
{

    public function store(Request $request)
    {
        try {
            return  Container::create([
                'user_id' => Auth::id(),
                'dispatch_number' => Container::getDispatchNumber(),
                'origin_country' => 'US',
                'origin_operator_name' => 'HERC',
                'postal_category_code' => 'A',
                'destination_operator_name' => $request->destination_operator_name,
                'unit_type' => $request->unit_type,
                'services_subclass_code' => $request->services_subclass_code
            ]);
        } catch (\Exception $ex) {
            $this->error = $ex->getMessage();
            return null;
        }
    }

    public function addOrderToContainer(Container $container, string $barcode)
    {
        $subString = strtolower(substr($barcode,0,2));
        if(strtolower(substr($barcode,0,2)) == 'na' || strtolower(substr($barcode,0,2)) == 'xl'|| strtolower(substr($barcode,0,2)) == 'nb'){
            $subString = 'nx';
        }
        if ($container->hasAnjunChinaService()) {
            return $this->toAnjunChinaContainer($container, $barcode);
        }
        $containerOrder = $container->orders->first();
        if ($containerOrder) {
            $client = new Client();
            $newResponse = $client->getModality($barcode);
            $oldResponse = $client->getModality($containerOrder->corrios_tracking_code);
            if ($newResponse != $oldResponse) {

                return $this->validationError404($barcode, 'Order Service is changed. Please Check Packet Service');
            }
        }
 
        $order = Order::where('corrios_tracking_code', strtoupper($barcode))->first();

       if (strtolower($container->getSubClassCode())  != $subString) {
            return $this->validationError404($barcode, 'Order Not Found. Please Check Packet Service');
        }

        if (!$order) {
            return $this->validationError404($barcode, 'Order Not Found.');
        }

        if ($order->status < Order::STATUS_PAYMENT_DONE) {
            return $this->validationError404($barcode, 'Please check the Order Status, either the order has been canceled, refunded or not yet paid');
        }

        return $this->updateContainer($container, $order, $barcode);
    }
    public function toAnjunChinaContainer(Container $container, string $barcode)
    {
        $order = Order::where('corrios_tracking_code', strtoupper($barcode))->first();

        if (!$order) {
            return $this->validationError404($barcode,  'Order Not Found.');
        }

        if ($order->status < Order::STATUS_PAYMENT_DONE) {
            return $this->validationError404($barcode, 'Please check the Order Status, either the order has been canceled, refunded or not yet paid');
        }
        if (!$order->shippingService->isAnjunChinaService()) {

            return $this->validationError404($barcode, 'Order does not belongs to this anjun china container Service. Please Check Packet Service 1');
        }
        
        if ($container->hasAnjunChinaStandardService() && !$order->shippingService->isAnjunChinaStandardService()) {

            return $this->validationError404($barcode, 'Order does not belongs to this anjun china standard container Service. Please Check Packet Service 2');
        }
        if ($container->hasAnjunChinaExpressService() && !$order->shippingService->isAnjunChinaExpressService()) {

            return $this->validationError404($barcode, 'Order does not belongs to this anjun china container express Service. Please Check Packet Service 3');
        }

        return $this->updateContainer($container, $order, $barcode);
    }
    public function updateContainer($container, $order, $barcode)
    {
        $containerOrder = $container->orders->first();
        if ($containerOrder) {
            if ($containerOrder->getOriginalWeight('kg') <= 3 && $order->getOriginalWeight('kg') > 3) {

                return $this->validationError404($barcode, 'Order weight is greater then 3 Kg, Please Check Order Weight');
            } elseif ($containerOrder->getOriginalWeight('kg') > 3 && $order->getOriginalWeight('kg') <= 3) {

                return $this->validationError404($barcode, 'Order weight is less then 3 Kg, Please Check Order Weight');
            }
        }

        if (!$order->containers->isEmpty()) {
            return $this->validationError404($barcode, 'Order Already in Container.');
        }

        $container->orders()->attach($order->id);

        $this->addOrderTracking($order->id);

        $order->error = null;
        $order->code = 200;

        return [
            'order' => (new PackageResource($order))
        ];
    }

    public function removeOrderFromContainer(Container $container, Order $order)
    {
        $container->orders()->detach($order->id);

        return $this->removeOrderTracking($order->id);
    }

    public function addOrderTracking($id)
    {
        OrderTracking::create([
            'order_id' => $id,
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

        return $order_tracking->delete();
    }
    public function validationError404($barcode, $message)
    {
        return [
            'order' => [
                'corrios_tracking_code' => $barcode,
                'error' => $message,
                'code' => 404
            ],
        ];
    }
}
