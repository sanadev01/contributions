<?php 

namespace App\Repositories\Warehouse;


class ChileContainerPackageRepository {


    public function addOrderToContainer($container, $order)
    {
        $container->orders()->attach($order->id);

        return $order;
    }

    public function removeOrderFromContainer($container, $id)
    {
        return $container->orders()->detach($id);
    }
}