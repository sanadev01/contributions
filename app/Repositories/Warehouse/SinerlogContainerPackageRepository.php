<?php 

namespace App\Repositories\Warehouse;
use App\Http\Resources\Warehouse\Container\PackageResource;
use App\Models\Order;
use App\Models\Warehouse\Container;
use App\Repositories\AbstractRepository;

class SinerlogContainerPackageRepository extends AbstractRepository {


    public function addOrderToContainer(Container $sinerlog_container,string $barcode)
    {
        if(explode('-', $sinerlog_container->services_subclass_code)[1]  != substr($barcode,0,2)){
            return [
                'order' => [
                    'corrios_tracking_code' => $barcode,
                    'error' => 'Order Not Found. Please Check Packet Service',
                    'code' => 404
                ],
            ];
        }
        $order = Order::where('corrios_tracking_code',strtoupper($barcode))->first();
        if ( !$order ){
            return [
                'order' => [
                    'corrios_tracking_code' => $barcode,
                    'error' => 'Order Not Found. Invalid BarCode',
                    'code' => 404
                ],
            ];
        }

        if ( !$order->containers->isEmpty() ){
            return [
                'order' => [
                    'corrios_tracking_code' => $barcode,
                    'error' => 'Order Already in Container.',
                    'code' => 409
                ],
            ];
        }

        $sinerlog_container->orders()->attach($order->id);

        $order->error = null;
        $order->code = 200;

        return [
            'order' => (new PackageResource($order))
        ];
    }

    public function removeOrderFromContainer(Container $sinerlog_container, Order $order)
    {
        return $sinerlog_container->orders()->detach($order->id);
    }
}