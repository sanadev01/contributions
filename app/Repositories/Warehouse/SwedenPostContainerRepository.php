<?php

namespace App\Repositories\Warehouse;

use App\Models\Warehouse\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ShippingService;

class SwedenPostContainerRepository {

    protected $error;

    public function get()
    {
        $query = Container::query();

        if ( !Auth::user()->isAdmin() ){
            $query->where('user_id',Auth::id());
        }

        return $query->where(function($query) {
            $query->where('services_subclass_code', ShippingService::Prime5);
        })->latest()->paginate();

    }

    public function store($request)
    {
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

    public function getError()
    {
        return $this->error;
    }
}
