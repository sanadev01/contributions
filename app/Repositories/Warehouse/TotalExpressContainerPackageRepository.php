<?php

namespace App\Repositories\Warehouse;

use App\Models\Order;
use App\Models\OrderTracking;
use App\Models\Warehouse\Container;
use Illuminate\Support\Facades\Session;


class TotalExpressContainerPackageRepository {

    public function addOrderToContainer($container, $order)
    {
        $error = null;

        if($container->services_subclass_code != $order->shippingService->service_sub_class){
            $error = 'container service does not match';
        }
        if(!$order->containers->isEmpty()) {
            $error = "Order is already present in Container";
        }
        if ($order->status != Order::STATUS_PAYMENT_DONE) {
            $error = 'Please check the Order Status, whether the order has been shipped, canceled, refunded, or not yet paid';
        }
        if ( (!$container->hasTotalExpressService() ||  !$order->shippingService->is_total_express())){

            $error = 'Order does not belong to this container. Please Check Packet Service';
        }
        if(!$container->orders()->where('order_id', $order->id)->first() && $error == null && $order->containers->isEmpty()) {
            $container->orders()->attach($order->id);
            $this->addOrderTracking($order);
                Session::flash('alert-class', 'alert-success');
                $message = 'Order Added in the Container Successfully';
            return [
                'success' => true,
                'message' => $message
            ];
        }
        Session::flash('alert-class', 'alert-danger');
        return [
            'success' => false,
            'message' => $error,

        ];
    }

    public function removeOrderFromContainer(Container $container, $id)
    {
        $order_tracking = OrderTracking::where('order_id', $id)->latest()->first();
        if($order_tracking) {
            $order_tracking->delete();
        }
        try {
            $container->orders()->detach($id);
            return true;
        } catch (\Exception $ex) {
            $this->error = $ex->getMessage();
            return false;
        }
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