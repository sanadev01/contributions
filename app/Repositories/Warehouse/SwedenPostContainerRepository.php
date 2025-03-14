<?php

namespace App\Repositories\Warehouse;

use App\Models\Warehouse\Container;
use Illuminate\Support\Facades\Auth;
use App\Models\ShippingService;
use App\Services\SwedenPost\Services\Container\DirectLinkReceptacle;
use Illuminate\Support\Facades\DB;

class SwedenPostContainerRepository
{

    protected $error;

    public function get()
    {
        $query = Container::query();
        return $query->where(function ($query) {
            $query->whereIn('services_subclass_code',[ShippingService::Prime5,ShippingService::Prime5RIO,ShippingService::DirectLinkAustralia,ShippingService::DirectLinkCanada,ShippingService::DirectLinkMexico,ShippingService::DirectLinkChile]);
        })->latest()->paginate(50);
    }

    public function store($request)
    {

        DB::beginTransaction();
        try { 

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
            if(!in_array($container->services_subclass_code,[ShippingService::Prime5,ShippingService::Prime5RIO]))
            {
                DB::commit();
                return $container;
            }
            
            $response =  (new DirectLinkReceptacle($container))->create($request->services_subclass_code);
            $data = $response->getData();
            if ($data->isSuccess) {
                $container->update([
                    'unit_code' => $data->output,
                    'dispatch_number' => $container->id
                ]);
                DB::commit();
                return $container;
            } else {
                DB::rollback();
                $this->error = $data->message;
                return null;
            } 
        } catch (\Exception $ex) {
            DB::rollback();
            $this->error = $ex->getMessage();
            return null;
        }
    }

    public function update(Container $container, $request)
    {
        try {
            return  $container->update([
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
            if ($force) {
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
