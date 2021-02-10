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
        $shippingservices = ShippingService::all();
        return $shippingservices;
    }

    public function store(Request $request)
    {   
        try{

            ShippingService::create(
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
                ])
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
                ])
            );

            return true;

        }catch(Exception $exception){
            session()->flash('alert-danger','Error while ShippingService');
            return null;
        }
    }
    

    public function delete(ShippingService $shippingService){

        $shippingService->delete();
        return true;

    }

}