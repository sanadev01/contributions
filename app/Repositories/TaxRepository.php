<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ShippingService;
use App\Models\User;
use App\Models\Order;
use App\Models\Tax;
use Exception;

class TaxRepository
{
    public function get()
    {
        $taxlist = ShippingService::all();
        return $taxlist;
    }

    public function getOrders(Request $request)
    {
        $orders = null;

        $trackingNumber = explode(',', preg_replace('/\s+/', '', $request->trackingNumbers));
        if($trackingNumber){
            $orders = Order::where('user_id',$request->user_id)->whereIn('corrios_tracking_code', $trackingNumber)->get();
        }
        return $orders;
    }

    public function store(Request $request)
    {
        $data = $request->all();
        dd($data);
        try{
            $insert = Tax::create( $request->except(['_token','user_name','tracking_code']));
            return $insert;
        }catch(Exception $exception){
            dd($exception->getMessage());
            session()->flash('alert-danger','Error while Adding Tax');
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
