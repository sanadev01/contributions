<?php

namespace App\Repositories\Warehouse;

use App\Models\Warehouse\Container;
use App\Repositories\AbstractRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        return $query->whereIn('services_subclass_code', ['NX','IX', 'XP','AJ-NX','AJ-IX'])->latest()->paginate(50);
    }

    public function store(Request $request)
    {
        try {

            if (in_array($request->services_subclass_code, [Container::CONTAINER_ANJUN_NX, Container::CONTAINER_ANJUN_IX]) ) {
                
                $latestAnujnContainer = Container::where('services_subclass_code', Container::CONTAINER_ANJUN_NX)
                                                    ->orWhere('services_subclass_code', Container::CONTAINER_ANJUN_IX)
                                                    ->latest()->first();

                $anjunDispatchNumber = ($latestAnujnContainer->dispatch_number > 1200000) ? $latestAnujnContainer->dispatch_number + 1 : 1200000;
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
                'services_subclass_code' => $request->services_subclass_code
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
}
