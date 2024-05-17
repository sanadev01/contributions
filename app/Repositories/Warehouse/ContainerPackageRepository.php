<?php
namespace App\Repositories\Warehouse;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\OrderTracking;
use App\Models\Warehouse\Container;
use Illuminate\Support\Facades\Auth;
use App\Repositories\AbstractRepository;

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
            
        $order = Order::where('corrios_tracking_code', strtoupper($barcode))->first();

    public function addOrderToContainer(Container $container, string $barcode)
    { 
        return (new AddContainerPackageRepository($container,$barcode))->addOrderToContainer();
    }
  
    public function removeOrderFromContainer(Container $container, Order $order)
    {
        $container->orders()->detach($order->id);

        return $this->removeOrderTracking($order->id);
    }
 

    public function removeOrderTracking($id)
    {

        $order_tracking = OrderTracking::where('order_id', $id)->latest()->first();

        return $order_tracking->delete();
    } 
     
}
