<?php

namespace App\Repositories\Warehouse;

use App\Models\Order;
use App\Models\OrderTracking;
use App\Services\GePS\Client;
use App\Models\Warehouse\Container;
use App\Services\Excel\Import\TrackingsImportService;


class GePSContainerPackageRepository {


    public function addOrderToContainer($container, $order)
    {
        $error = null;

        if(!$order->containers->isEmpty()) {
            $error = "Order is already present in Container";
        }
        if ($order->status != Order::STATUS_PAYMENT_DONE) {
            $error = 'Please check the Order Status, whether the order has been shipped, canceled, refunded, or not yet paid';
        }
        if ( (!$container->hasGePSService() && $order->shippingService->isGePSService()) 
            || ($container->hasGePSService() && !$order->shippingService->isGePSService())){

            $error = 'Order does not belong to this container. Please Check Packet Service';
        }
        if(!$container->orders()->where('order_id', $order->id)->first() && $error == null && $order->containers->isEmpty()) {
            $container->orders()->attach($order->id);
            $this->addOrderTracking($order);
            $gepsClient = new Client();   
            $response = $gepsClient->confirmShipment($order->corrios_tracking_code);
            if (!$response['success']) {
                $message = "Order Added in the Container Successfully, But ".$response['message'];
            }else{
                $message = 'Order Added in the Container Successfully';
            }
            return [
                'success' => false,
                'message' => $message
            ];
        }
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

    public function addTrackings($request, $id)
    {
        $container = Container::find($id);
        try{
            $file = $request->file('csv_file');
            try {
                $importTrackingService = new TrackingsImportService($file, $container);
                $importTrackingService->handle();
                if($container) {
                    session()->flash('alert-success', 'Trackings has been Uploaded Successfully');
                    return back();
                }
                return true;
            } catch (\Exception $exception) {
                throw $exception;
                session()->flash('alert-danger', 'Error While Uploading Trackings');
                return back();
            }
        }catch(Exception $exception){
            session()->flash('alert-danger','Error while Upload: '.$exception->getMessage());
            return null;
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