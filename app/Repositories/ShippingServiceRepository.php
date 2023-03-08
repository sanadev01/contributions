<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ShippingService;
use App\Models\User;
use Exception;

class ShippingServiceRepository
{
    public function get()
    {   
        $shippingservices = ShippingService::orderBy('name')->get();
        return $shippingservices;
    }

    public function store(Request $request)
    {   
        try{
            if($request->api == 'sinerlog') {
                $keys = array(
                    'name',
                    'max_length_allowed',
                    'max_width_allowed',
                    'min_width_allowed',
                    'min_length_allowed',
                    'max_sum_of_all_sides',
                    'max_weight_allowed',
                    'contains_battery_charges',
                    'contains_perfume_charges',
                    'contains_flammable_liquid_charges',
                    'active',
                    'api',
                    'service_sub_class',
                    'delivery_time',
                    'max_sum_of_all_products',
                    'service_api_alias',
                    'min_height_allowed',
                    'max_height_allowed'
                );
            }
            else {
                $keys = array(
                    'name',
                    'max_length_allowed',
                    'max_width_allowed',
                    'min_width_allowed',
                    'min_length_allowed',
                    'max_sum_of_all_sides',
                    'max_weight_allowed',
                    'contains_battery_charges',
                    'contains_perfume_charges',
                    'contains_flammable_liquid_charges',
                    'active',
                    'service_sub_class',
                    'delivery_time',
                );
            }
            
            ShippingService::create(
                $request->only($keys)
            );

            return true;

        }catch(Exception $exception){
            session()->flash('alert-danger','Error while Saving ShippingService');
            return null;
        }
    }

    public function update(Request $request,ShippingService $shippingService)
    {   
        
        try{

            $shippingService->update(
                $request->only([
                    'name',
                    'max_length_allowed',
                    'max_width_allowed',
                    'min_width_allowed',
                    'min_length_allowed',
                    'max_sum_of_all_sides',
                    'max_weight_allowed',
                    'contains_battery_charges',
                    'contains_perfume_charges',
                    'contains_flammable_liquid_charges',
                    'active',
                    'service_sub_class',
                    'delivery_time',
                ])
            );

            return true;

        }catch(Exception $exception){
            session()->flash('alert-danger','Error while ShippingService');
            return null;
        }
    }
    

    public function delete(ShippingService $shippingService)
    {
        $shippingService->rates()->delete();

        $shippingService->delete();
        return true;

    }

}