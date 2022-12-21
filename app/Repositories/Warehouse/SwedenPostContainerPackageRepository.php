<?php

namespace App\Repositories\Warehouse;

use App\Models\OrderTracking;


class SwedenPostContainerPackageRepository {


    public function addOrderToContainer($container, $order)
    {
        $container->orders()->attach($order->id);

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
}