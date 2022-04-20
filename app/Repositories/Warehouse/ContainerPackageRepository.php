<?php 

namespace App\Repositories\Warehouse;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\OrderTracking;
use App\Models\Warehouse\Container;
use Illuminate\Support\Facades\Auth;
use App\Repositories\AbstractRepository;
use App\Http\Resources\Warehouse\Container\PackageResource;

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
        $subString = (strtolower(substr($barcode,0,2)) == 'na') ? 'nx' : strtolower(substr($barcode,0,2));

        if(strtolower($container->services_subclass_code)  != $subString){
            return [
                'order' => [
                    'corrios_tracking_code' => $barcode,
                    'error' => 'Order Not Found. Please Check Packet Service',
                    'code' => 404
                ],
            ];
        }
        $containerOrder = $container->orders->first();
        $order          = Order::where('corrios_tracking_code',strtoupper($barcode))->first();
        
        if ($order->status < Order::STATUS_PAYMENT_DONE) {
            return [
                'order' => [
                    'corrios_tracking_code' => $barcode,
                    'error' => 'Please check the Order Status, either the order has been canceled, refunded or not yet paid',
                    'code' => 404
                ],
            ];
        }

        if( $containerOrder ){
            if( $containerOrder->getOriginalWeight('kg') <= 3 && $order->getOriginalWeight('kg') > 3){
                return [
                    'order' => [
                        'corrios_tracking_code' => $barcode,
                        'error' => 'Order weight is greater then 3 Kg, Please Check Order Weight',
                        'code' => 404
                    ],
                ];
            }elseif($containerOrder->getOriginalWeight('kg') > 3 && $order->getOriginalWeight('kg') <= 3)
            {
                return [
                    'order' => [
                        'corrios_tracking_code' => $barcode,
                        'error' => 'Order weight is less then 3 Kg, Please Check Order Weight',
                        'code' => 404
                    ],
                ];
            }
        }
        
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

        $this->addOrderTracking($order->id);

        $order->error = null;
        $order->code = 200;

        return [
            'order' => (new PackageResource($order))
        ];
    }

    public function removeOrderFromContainer(Container $container, Order $order)
    {
        $container->orders()->detach($order->id);

        return $this->removeOrderTracking($order->id);
    }

    public function addOrderTracking($id)
    {
        OrderTracking::create([
            'order_id' => $id,
            'status_code' => Order::STATUS_INSIDE_CONTAINER,
            'type' => 'HD',
            'description' => 'Parcel inside Homedelivery Container',
            'country' => 'US',
            'city' => 'Miami'
        ]);

        return true;
    }

    public function removeOrderTracking($id)
    {

        $order_tracking = OrderTracking::where('order_id', $id)->latest()->first();

       return $order_tracking->delete();

    }
}