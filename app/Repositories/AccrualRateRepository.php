<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use App\Models\Warehouse\AccrualRate;

class AccrualRateRepository
{
    public function get(Request $request)
    {   
        $query = AccrualRate::query();

        if($request->service){
            $query->where('service', $request->service);
        }
        if($request->country_id){
            $query->where('country_id', $request->country_id);
        }
        if($request->weight){
            $query->where('weight', 'LIKE', "%{$request->weight}%");
        }
        if($request->cwb){
            $query->where('cwb', 'LIKE', "%{$request->cwb}%");
        }
        if($request->gru){
            $query->where('gru', 'LIKE', "%{$request->gru}%");
        }
        if($request->commission){
            $query->where('commission', 'LIKE', "%{$request->commission}%");
        }

        return $query->get();
    }

}