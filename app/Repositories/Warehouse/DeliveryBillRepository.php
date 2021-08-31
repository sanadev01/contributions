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
    public function get()
    {
        $query = DeliveryBill::query();

        return $query->latest()->paginate(50);
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

            $isNX = false;
            $isIX = false;
            $isXP = false;
            foreach($request->get('container',[]) as $containerId){
                $container = Container::find($containerId)->services_subclass_code;
                if($container  == "NX"){
                    $isNX = true;
                }
                if($container  == "IX"){
                    $isIX = true;
                }
                if($container  == "XP"){
                    $isXP = true;
                }
            }
            if( ($isNX === true && $isIX === true) || ($isNX === true && $isXP === true) || ($isIX === true && $isXP === true)){
                throw new \Exception("Please don't use diffirent type of Container in one Delivery Bill",500);
            }

            $deliveryBill = DeliveryBill::create([
                'name' => 'Delivery BillL: '.Carbon::now()->format('m-d-Y'),
            ]);

            $deliveryBill->containers()->sync($request->get('container',[]));
            
            foreach($deliveryBill->containers()->get() as $containers){
                $containers->orders()->update([
                    'status' =>  80,
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

            $isNX = false;
            $isIX = false;
            $isXP = false;
            foreach($request->get('container',[]) as $containerId){
                $container = Container::find($containerId)->services_subclass_code;
                if($container  == "NX"){
                    $isNX = true;
                }
                if($container  == "IX"){
                    $isIX = true;
                }
                if($container  == "XP"){
                    $isXP = true;
                }
            }
            if( ($isNX === true && $isIX === true) || ($isNX === true && $isXP === true) || ($isIX === true && $isXP === true)){
                throw new \Exception("Please don't use diffirent type of Container in one Delivery Bill",500);
            }

            $deliveryBill->containers()->sync($request->get('container',[]));
            
            foreach($deliveryBill->containers()->get() as $containers){
                $containers->orders()->update([
                    'status' =>  80,
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
            'description' => 'Homedelivery sent parcel to airport with CN35',
            'country' => 'United States',
            'city' => 'Miami'
        ]);

        return true;
    }
}
