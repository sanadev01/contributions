<?php

namespace App\Repositories\Warehouse;

use Illuminate\Http\Request;
use App\Facades\MileExpressFacade;
use App\Models\Warehouse\Container;
use Illuminate\Support\Facades\Auth;
use App\Repositories\AbstractRepository;

class ContainerRepository extends AbstractRepository{

    public function get(Request $request)
    {
        $query = Container::query();

        if ( !Auth::user()->isAdmin() ){
            $query->where('user_id',Auth::id());
        }
        if($request->filled('dispatchNumber')){
           $query->where('dispatch_number', 'LIKE', '%' . $request->dispatchNumber . '%');
        } 
        if($request->filled('sealNo')){
          $query->where('seal_no', 'LIKE', '%' . $request->sealNo . '%');
        } 
        if($request->filled('packetType')){
            $query->where('services_subclass_code', 'LIKE', '%' . $request->packetType . '%');
        }
        if($request->filled('unitCode')){
            $query->where('unit_code', 'LIKE', '%' . $request->unitCode . '%');
        }
        
        if ($request->has('typeMileExpress')) {
            return $query->whereIn('services_subclass_code', ['ML-EX'])->latest()->paginate(50);
        }
        
        return $query->whereIn('services_subclass_code', ['NX','IX', 'XP','AJ-NX','AJ-IX'])->latest()->paginate(50);
    }

    public function store(Request $request)
    {
        try {

            if (in_array($request->services_subclass_code, [Container::CONTAINER_ANJUN_NX, Container::CONTAINER_ANJUN_IX]) ) {
                
                $latestAnujnContainer = Container::where('services_subclass_code', Container::CONTAINER_ANJUN_NX)
                                                    ->orWhere('services_subclass_code', Container::CONTAINER_ANJUN_IX)
                                                    ->latest()->first();

                $anjunDispatchNumber = ($latestAnujnContainer) ? $latestAnujnContainer->dispatch_number + 1 : 900000;
            }

            if ($request->services_subclass_code == Container::CONTAINER_MILE_EXPRESS) {
                $response = MileExpressFacade::createContainer($request);

                if ($response->success == false) {
                    $this->error = $response->error;
                    return false;
                }

                $mileExpressContinerData = $response->data['data'];
            }

            $container =  Container::create([
                'user_id' => Auth::id(),
                'dispatch_number' => 0,
                'origin_country' => 'US',
                'seal_no' => $request->seal_no,
                'origin_operator_name' => 'HERC',
                'postal_category_code' => 'A',
                'destination_operator_name' => $request->destination_operator_name,
                'unit_type' => $request->unit_type,
                'services_subclass_code' => $request->services_subclass_code,
                'unit_response_list' => ($request->services_subclass_code == Container::CONTAINER_MILE_EXPRESS) ? json_encode($mileExpressContinerData) : null,
            ]);

            $container->update([
                'dispatch_number' => ($container->hasAnjunService()) ? $anjunDispatchNumber : $container->id,
            ]);

            return $container;

        } catch (\Exception $ex) {
            $this->error = $ex->getMessage();
            return null;
        }
    }

    public function update(Container $container, Request $request)
    {
        try {
            return  $container->update([
                'destination_operator_name' => $request->destination_operator_name,
                'seal_no' => $request->seal_no,
                'unit_type' => $request->unit_type
            ]);

        } catch (\Exception $ex) {
            $this->error = $ex->getMessage();
            return null;
        }
    }

    public function delete(Container $container, bool $force = false)
    {
        try {
            if ( $force ){
                $container->forceDelete();
            }

            $container->deliveryBills()->delete();
            $container->orders()->sync([]);
            $container->delete();

            return true;
        } catch (\Exception $ex) {
            $this->error = $ex->getMessage();
            return null;
        }
    }

    public function addOrderToContainer($container, $orderId)
    {
        try {
            $container->orders()->attach($orderId);
            return true;
        } catch (\Exception $ex) {
            $this->error = $ex->getMessage();
            return false;
        }
    }

    public function removeOrderFromContainer($container, $id)
    {
        try {
            $container->orders()->detach($id);
            return true;
        } catch (\Exception $ex) {
            $this->error = $ex->getMessage();
            return false;
        }
    }

    public function getAirWayBillIdsForMileExpress($containerOrders)
    {
        $airWayBillIds = [];
        
        foreach ($containerOrders as $order) {
            $orderApiResponse = json_decode($order->api_response);
            
            array_push($airWayBillIds, $orderApiResponse->data->volumes[0]->air_waybill_id);
        }

        return $airWayBillIds;
    }
}
