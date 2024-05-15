<?php

namespace App\Repositories\Warehouse;

use App\Models\Order;
use App\Models\OrderTracking;
use App\Services\TotalExpress\Client;
use App\Models\Warehouse\Container;
use Illuminate\Support\Facades\Session;


class ContainerPackageFactoryRepository
{

    public function addOrderToContainer($container, $order)
    {
        $message = null;
        $success = false;

        if ($container->services_subclass_code != $order->shippingService->service_sub_class) {
            $message = "This is " . $container->getServiceSubClass() . " packages container.You put $order->carrier package";
        } elseif (!$order->containers->isEmpty()) {
            $message = "Order is already present in Container";
        } elseif ($order->status != Order::STATUS_PAYMENT_DONE) {
            $message = 'Please check the Order Status, whether the order has been shipped, canceled, refunded, or not yet paid';
        } else {

            // $apiOrderId = json_decode($order->api_response)->orderResponse->data->id;
            // $totalClient = new Client();   
            // $response = $totalClient->dispatchShipment($apiOrderId); 
            if (true) {
                $success = true;
                $message = 'added successfully';
                $container->orders()->attach($order->id);
                $this->addOrderTracking($order);
            } else {
                $success = false;
                $message = 'server error';
            }
        }
        Session::flash('alert-class', $success ? 'alert-success' : 'alert-danger');
        Session::flash('message', $message);
        return [
            'success' => $success,
            'message' => $message,

        ];
    }

    public function removeOrderFromContainer(Container $container, $id)
    {
        $order_tracking = OrderTracking::where('order_id', $id)->latest()->first();
        if ($order_tracking) {
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
