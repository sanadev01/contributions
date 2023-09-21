<?php

namespace App\Repositories\Warehouse;

use App\Models\Warehouse\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ShippingService;

class GDEContainerRepository {

<<<<<<<< HEAD:app/Repositories/Warehouse/GDEContainerRepository.php
    protected $error;

    public function get()
========
    public function get(Request $request, $paginate)
>>>>>>>> 148a108bb953eb768579be0a29bd022d7e94a8a6:app/Repositories/Warehouse/ContainerRepository.php
    {
        $query = Container::query();

        if ( !Auth::user()->isAdmin() ){
            $query->where('user_id',Auth::id());
        }
<<<<<<<< HEAD:app/Repositories/Warehouse/GDEContainerRepository.php

        return $query->where(function($query) {
            $query->whereIn('services_subclass_code', [ShippingService::GDE_PRIORITY_MAIL, ShippingService::GDE_FIRST_CLASS]);
        })->latest()->paginate();

    }
========
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
        if($request->filled('startDate')||$request->filled('endDate')){ 
            $query->whereBetween('created_at', [$request->startDate??date('2020-01-01'), $request->endDate??date('Y-m-d')]);
        } 
        $services = ['NX','IX', 'XP','AJ-NX','AJ-IX'];
        if($request->filled('service')){
             $services = json_decode($request->service);
        }
        $query->whereIn('services_subclass_code', $services)->latest();
        
        $query = $paginate ? $query->paginate(50) : $query->where('unit_code', '!=', null )->get();

        return $query;
     }
>>>>>>>> 148a108bb953eb768579be0a29bd022d7e94a8a6:app/Repositories/Warehouse/ContainerRepository.php

    public function store($request)
    {
        try {
            $container =  Container::create([
                'user_id' => Auth::id(),
                'dispatch_number' => 0,
                'origin_country' => 'US',
                'origin_airport' => $request->origin_airport,
                'flight_number' => $request->flight_number,
                'seal_no' => $request->seal_no,
                'origin_operator_name' => 'HERC',
                'postal_category_code' => 'A',
                'destination_operator_name' => $request->destination_operator_name,
                'unit_type' => $request->unit_type,
                'services_subclass_code' => $request->services_subclass_code
            ]);

            $container->update([
                'dispatch_number' => $container->id
            ]);

            return $container;

        } catch (\Exception $ex) {
            $this->error = $ex->getMessage();
            return null;
        }
    }

    public function update(Container $container, $request)
    {
        try {
            return  $container->update([
                'seal_no' => $request->seal_no,
                'unit_type' => $request->unit_type,                
                'origin_airport' => $request->origin_airport,
                'flight_number' => $request->flight_number,
                'destination_operator_name' => $request->destination_operator_name
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

    public function getError()
    {
        return $this->error;
    }
}
