<?php

namespace App\Repositories\Warehouse;

use App\Models\Order;
use App\Models\OrderTracking;
use App\Models\Warehouse\Container;
use App\Services\Excel\Import\TrackingsImportService;


class GePSContainerPackageRepository {


    public function addOrderToContainer($id, $barcode)
    {
        $error = null;
        $order = Order::where('corrios_tracking_code', $barcode)->first();
        $container = Container::find($id);

        if(!$order->containers->isEmpty()) {
            $error = "Order is already present in Container";
        }
        if ($order->status != Order::STATUS_PAYMENT_DONE) {
            $error = 'Please check the Order Status, either the order has been canceled, refunded or not yet paid';
        }
        if ($container->hasGePSService() && !$order->shippingService->isGePSService()) {
            $error = 'Order does not belong to this container. Please Check Packet Service';
        }

        if (!$container->hasGePSService() && $order->shippingService->isGePSService()) {
            $error = 'Order does not belong to this container. Please Check Packet Service';
        }
        if(!$container->orders()->where('order_id', $order->id)->first() && $error == null) {
            $container->orders()->attach($order->id);
        }
        \Log::info($error);
        return $order;
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
        // dd($container); 
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
}