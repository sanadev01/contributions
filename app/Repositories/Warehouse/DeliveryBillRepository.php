<?php


namespace App\Repositories\Warehouse;


use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\OrderTracking;
use App\Models\Warehouse\Container;
use Illuminate\Support\Facades\Auth;
use App\Models\Warehouse\DeliveryBill;
use App\Repositories\AbstractRepository;

class DeliveryBillRepository extends AbstractRepository
{
    public function get(Request $request, $isPaginate)
    {
        $query = DeliveryBill::query();
        
        if ( !Auth::user()->isAdmin() ){
            $query->where('user_id',Auth::id());
        }
        if ($request->type){
            $query->whereHas('containers', function ($query) use ($request) {
                return $query->whereIn('services_subclass_code', json_decode($request->type));
            });
        }
        if ($request->startDate) {
            $startDate = $request->startDate . ' 00:00:00';
            $query->where('created_at', '>=', $startDate);
        }
        if ($request->endDate) {
            $endDate = $request->endDate . ' 23:59:59';
            $query->where('created_at', '<=', $endDate);
        }
        $deliveryBill = $query->latest();
        return $isPaginate ? $deliveryBill->paginate(50) : $deliveryBill->get();
    }

    public function getContainers()
    {
        $query = Container::query()->registered();

        if ( !Auth::user()->isAdmin() ){
            $query->where('user_id',Auth::id());
        }

        $query->whereDoesntHave('deliveryBills');

        return $query->get();
    }

    public function store(Request $request)
    {
        try {

            $containerService = null;

            foreach($request->get('container',[]) as $containerId){
                $container = Container::find($containerId)->services_subclass_code;
                
                if ($container && !$containerService) {
                    $containerService = $container;
                }

                if($container && $containerService != $container){
                    throw new \Exception("Please don't use diffirent type of Container in one Delivery Bill",500);
                }
            }

            $deliveryBill = DeliveryBill::create([
                'name' => 'Delivery BillL: '.Carbon::now()->format('m-d-Y'),
                'user_id' => Auth::id()
            ]);

            $deliveryBill->containers()->sync($request->get('container',[]));
            
            foreach($deliveryBill->containers()->get() as $containers){
                $containers->orders()->update([
                    'status' =>  Order::STATUS_SHIPPED,
                    'api_tracking_status' => 'HD-Shipped',
                ]);

                foreach($containers->orders as $order)
                { 
                    $this->addOrderTracking($order->id);
                }
            }

            return $deliveryBill;
        }catch (\Exception $exception){
            $this->error = $exception->getMessage();
            return  null;
        }
    }

    public function update(Request $request, DeliveryBill $deliveryBill)
    {
        try {

            $containerService = null;

            foreach($request->get('container',[]) as $containerId){
                $container = Container::find($containerId)->services_subclass_code;
                
                if ($container && !$containerService) {
                    $containerService = $container;
                }

                if($container && $containerService != $container){
                    throw new \Exception("Please don't use diffirent type of Container in one Delivery Bill",500);
                }
            }

            $deliveryBill->containers()->sync($request->get('container',[]));
            
            foreach($deliveryBill->containers()->get() as $containers){
                $containers->orders()->update([
                    'status' =>  Order::STATUS_SHIPPED,
                ]);
            }
            return $deliveryBill;
        }catch (\Exception $exception){
            $this->error = $exception->getMessage();
            return  null;
        }
    }

    public function delete(DeliveryBill $deliveryBill)
    {
        try {

            $deliveryBill->containers()->sync([]);
            $deliveryBill->delete();

            return true;

        }catch (\Exception $exception){
            $this->error = $exception->getMessage();
            return null;
        }
    }

    public function addOrderTracking($order_id)
    {
        OrderTracking::create([
            'order_id' => $order_id,
            'status_code' => Order::STATUS_SHIPPED,
            'type' => 'HD',
            'description' => 'Parcel transfered to airline',
            'country' => 'US',
            'city' => 'Miami'
        ]);

        return true;
    }
}
