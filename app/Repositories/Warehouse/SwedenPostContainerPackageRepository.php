<?php

namespace App\Repositories\Warehouse;

use App\Models\Order;
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

        if (!$order->containers->isEmpty()) {
            $error = "Order is already present in Container";
        }
        if ($order->status != Order::STATUS_PAYMENT_DONE) {
            $error = 'Please check the Order Status, whether the order has been shipped, canceled, refunded, or not yet paid';
        }

        if ((!$container->hasSwedenPostService() && $order->shippingService->isSwedenPostService())
            || ($container->hasSwedenPostService() && !$order->shippingService->isSwedenPostService())
        ) {
            $error = 'Order does not belong to this container. Please Check Packet Service';
        }
        if (!$container->orders()->where('order_id', $order->id)->first() && $error == null && $order->containers->isEmpty()) {

            $response =  (new DirectLinkReceptacle($this->container))->scanItem($this->barcode);
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
        DB::baginTransaction();
        $order_tracking = OrderTracking::where('order_id', $id)->latest()->first();
        if ($order_tracking) {
            $order_tracking->delete();
        }

        try {
            $order = Order::find($id);
            $response =  (new DirectLinkReceptacle($this->container))->removeItem($order->corrios_tracking_code);
            $data = $response->getData();
            if ($data->isSuccess) {
                $container->orders()->detach($id);
                 DB::commit();
                return true;
            } else {
                DB::rollback();
                return false;
            }
        } catch (\Exception $ex) {
            $this->error = $ex->getMessage();
            return false;
        }
    }

    public function addTrackings($request, $id)
    {
        $container = Container::find($id);
        try {
            $file = $request->file('csv_file');
            try {
                $importTrackingService = new TrackingsImportService($file, $container);
                $importTrackingService->handle();
                if ($container) {
                    session()->flash('alert-success', 'Trackings has been Uploaded Successfully');
                    return back();
                }
                return true;
            } catch (\Exception $exception) {
                throw $exception;
                session()->flash('alert-danger', 'Error While Uploading Trackings');
                return back();
            }
        } catch (\Exception $exception) {
            session()->flash('alert-danger', 'Error while Upload: ' . $exception->getMessage());
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


    public function removeOrderFromContainer($container, $id)
    {
        $order_tracking = OrderTracking::where('order_id', $id)->latest()->first();
        if ($order_tracking) {
            $order_tracking->delete();
        }
        return $container->orders()->detach($id);
    }

    public function addOrderToContainer($container, $order)
    {
        $container->orders()->attach($order->id);
        return $order;
    }
}
