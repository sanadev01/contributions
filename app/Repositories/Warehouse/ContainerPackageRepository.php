<?php 

namespace App\Repositories\Warehouse;

use App\Http\Resources\Warehouse\Container\PackageResource;
use App\Models\Order;
use App\Models\Warehouse\Container;
use App\Repositories\AbstractRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContainerPackageRepository extends AbstractRepository{

    public function store(Request $request)
    {
        try {
            return  Container::create([
                'user_id' => Auth::id(),
                'dispatch_number' => Container::getDispatchNumber(),
                'origin_country' => 'US',
                'origin_operator_name' => 'HERC',
                'postal_category_code' => 'A',
                'destination_operator_name' => $request->destination_operator_name,
                'unit_type' => $request->unit_type,
                'services_subclass_code' => $request->services_subclass_code
            ]);

        } catch (\Exception $ex) {
            $this->error = $ex->getMessage();
            return null;
        }
    }

    public function addOrderToContainer(Container $container,string $barcode)
    {
        // $serviceCode = ;
        if($container->services_subclass_code  != substr($barcode,0,2)){
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

        $container->orders()->attach($order->id);

        $order->error = null;
        $order->code = 200;

        return [
            'order' => (new PackageResource($order))
        ];
    }

    public function removeOrderFromContainer(Container $container, Order $order)
    {
        return $container->orders()->detach($order->id);
    }
}