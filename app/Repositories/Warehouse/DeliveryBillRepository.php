<?php


namespace App\Repositories\Warehouse;


use App\Models\Warehouse\Container;
use App\Models\Warehouse\DeliveryBill;
use App\Repositories\AbstractRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

            return $deliveryBill;
        }catch (\Exception $exception){
            $this->error = $exception->getMessage();
            return  null;
        }
    }

    public function update(Request $request, DeliveryBill $deliveryBill)
    {
        try {

            $deliveryBill->containers()->sync($request->get('container',[]));

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
}
