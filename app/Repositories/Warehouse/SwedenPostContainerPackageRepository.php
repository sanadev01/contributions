<?php

namespace App\Repositories\Warehouse;

use App\Models\Order;
use App\Models\ShippingService;
use App\Models\OrderTracking;
use App\Models\Warehouse\Container;
use App\Services\Excel\Import\TrackingsImportService;
use App\Services\SwedenPost\Services\Container\DirectLinkReceptacle;
use Illuminate\Support\Facades\DB;

class SwedenPostContainerPackageRepository
{
    public function addOrderToPackageContainer($container, $order)
    {
        $shippingService = $order->shippingService;
        if (count($order->containers)) {
            return [
                'success' => false,
                'message' =>  "Order is already present in Container"
            ];
        }


        if ($order->status != Order::STATUS_PAYMENT_DONE) {
            return [
                'success' => false,
                'message' => 'Please check the Order Status, whether the order has been shipped, canceled, refunded, or not yet paid'
            ];
        }
        if ($this->isOrderBelongsToDLContainer($container, $shippingService, $order)) {
            $this->assignOrderToContainer($container, $order);
            return [
                'success' => true,
                'message' => 'Added Successfully',
            ];
        }
        if (!$this->isOrderBelongToContainer($container, $shippingService)) {
            return [
                'success' => false,
                'message' => 'Order does not belong to this container. Please Check Packet Service',
            ];
        }

        $response =  (new DirectLinkReceptacle($container))->scanItem($order->corrios_tracking_code);
        $data = $response->getData();
        if ($data->isSuccess) {
            $this->assignOrderToContainer($container, $order);
            return [
                'success' => true,
                'message' => $data->message,
            ];
        } else {
            return [
                'success' => false,
                'message' => $data->message
            ];
        }
    }
    public function removeOrderFromPackageContainer(Container $container, $id)
    {
        DB::beginTransaction();
        try {
            $order = Order::find($id);
            if ($container->is_directlink_country) {
                return $this->detachOrder($id, $container);
            }
            $response =  (new DirectLinkReceptacle($container))->removeItem($order->corrios_tracking_code);
            $data = $response->getData();
            if ($data->isSuccess) {
                return $this->detachOrder($id, $container);
            } else {
                DB::rollback();
                return false;
            }
        } catch (\Exception $ex) {
            return false;
        }
    }
    protected function assignOrderToContainer($container, $order)
    {
        $container->orders()->attach($order->id);
        $this->addOrderTracking($order);
    }
    protected function isOrderBelongToContainer($container, $shippingService)
    {
        if ($container->has_sweden_post_service) {
            return $shippingService->is_sweden_post_service;
        } elseif ($container->is_directlink_country) {
            return $shippingService->is_directlink_country;
        } else
            return false;
    }
    // Additional methods for conditions
    protected function isOrderBelongsToDLContainer($container, $shippingService, $order)
    {
        return $container->is_directlink_country &&
            $shippingService->is_directlink_country &&
            $this->countryContainerMatched(optional(optional($order->recipient)->country)->code, $container->services_subclass_code);
    }
    function countryContainerMatched($countryCode, $subClassCode)
    {
        if ($countryCode == 'MX') {
            return  $subClassCode == ShippingService::DirectLinkMexico;
        }
        if ($countryCode == 'CL') {
            return  $subClassCode == ShippingService::DirectLinkChile;
        }
        if ($countryCode == 'AU') {
            return   $subClassCode == ShippingService::DirectLinkAustralia;
        }
        if ($countryCode == 'CA') {
            return $subClassCode == ShippingService::DirectLinkCanada;
        }
        return false;
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

    public function detachOrder($orderId, $container)
    {
        $order_tracking = OrderTracking::where('order_id', $orderId)->latest()->first();
        if ($order_tracking) {
            $order_tracking->delete();
        }
        $container->orders()->detach($orderId);

        DB::commit();
        return true;
    }
}
