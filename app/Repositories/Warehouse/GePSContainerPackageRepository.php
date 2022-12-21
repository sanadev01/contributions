<?php

namespace App\Repositories\Warehouse;

use App\Models\OrderTracking;
use App\Models\Warehouse\Container;
use App\Services\Excel\Import\TrackingsImportService;


class GePSContainerPackageRepository {


    public function addOrderToContainer($container, $order)
    {
        if(!$container->orders()->where('order_id', $order->id)->first()) {
            $container->orders()->attach($order->id);
        }
        return $order;
    }

    public function removeOrderFromContainer($container, $id)
    {
        $order_tracking = OrderTracking::where('order_id', $id)->latest()->first();
        if($order_tracking) {
            $order_tracking->delete();
        }
        return $container->orders()->detach($id);
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