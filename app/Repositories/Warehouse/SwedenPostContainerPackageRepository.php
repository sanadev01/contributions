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
        $error = null; 
        if (count($order->containers)){
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
        if($container->is_directlink_country && $order->shippingService->is_directlink_country && $this->countryCotainerMatched(optional(optional($order->recipient)->country)->code,$container)){
            $container->orders()->attach($order->id);
            $this->addOrderTracking($order);
            return [
                'success' => true,
                'message' => 'Added Successfully',
            ];
        }
        if ((!$container->hasSwedenPostService() || !$order->shippingService->isSwedenPostService()) || $order->shippingService->is_directlink_country){
            return [
                'success' => false,
                'message' =>'Order does not belong to this container. Please Check Packet Service',
            ];
        }

        if ((!$container->hasSwedenPostService() || !$order->shippingService->isSwedenPostService())){
            $error = 'Order does not belong to this container. Please Check Packet Service';
        }
        if (!$container->orders()->where('order_id', $order->id)->first() && $error == null && $order->containers->isEmpty()) {

            $response =  (new DirectLinkReceptacle($container))->scanItem($order->corrios_tracking_code);
            $data = $response->getData();
            if ($data->isSuccess) {
                $container->orders()->attach($order->id);
                $this->addOrderTracking($order);
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
        return [
            'success' => false,
            'message' => $error
        ];
    }

    public function removeOrderFromPackageContainer(Container $container, $id)
    {
        DB::beginTransaction(); 
        try {
            $order = Order::find($id); 
            if($container->is_directlink_country){
                return $this->detachOrder($id,$container);
            }
            $response =  (new DirectLinkReceptacle($container))->removeItem($order->corrios_tracking_code);
            $data = $response->getData();
            if ($data->isSuccess) {  
                return $this->detachOrder($id,$container);
            } else {
                DB::rollback();
                return false;
            }
        } catch (\Exception $ex) {
            return false;
        }
    }
    function countryCotainerMatched($countryCode,$container) {
    if($countryCode =='MX' ){
        return $container->services_subclass_code == ShippingService::DirectLinkMexico;
    } if($countryCode =='CL' ){
        return  $container->services_subclass_code == ShippingService::DirectLinkChile;
    } if($countryCode =='AU' ){
        return   $container->services_subclass_code == ShippingService::DirectLinkAustralia;
    } if($countryCode =='CA' ){
        return $container->services_subclass_code == ShippingService::DirectLinkCanada;
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
    
    public function detachOrder($orderId,$container) {
        $order_tracking = OrderTracking::where('order_id', $orderId)->latest()->first();
        if ($order_tracking) {
            $order_tracking->delete();
        }
        $container->orders()->detach($orderId);

        DB::commit();
        return true;
        
    }
}
