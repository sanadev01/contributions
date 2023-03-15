<?php

namespace App\Repositories;

use App\Models\AffiliateSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AffiliateSaleRepository
{
    public function get(Request $request,$paginate = true,$pageSize=50){
        $query = AffiliateSale::has('user')->with('order')->has('order');

        if (Auth::user()->isUser()) {
            $query->where('user_id', Auth::id())->where('referrer_id', $request->user_id);
        }else {
            $query->where('user_id', $request->user_id);
        }

        if ( $request->start ){
            $startDate = $request->start . ' 00:00:00';
            $query->where(function($query) use($startDate){
                return $query->where('created_at','>',$startDate);
            });
        }
        
        if ( $request->end ){
            $endDate = $request->end.' 23:59:59';
            $query->where(function($query) use($endDate){
                return $query->where('created_at','<=', $endDate);
            });
        }

        if ( $request->search ){
            $query->where('type',"$request->search")
                ->orWhere('value',"{$request->search}")
                ->orWhere('order_id', 'LIKE', "%{$request->search}%")
                ->orWhere('commission', 'LIKE', "%{$request->search}%");
           
            $query->orWhereHas('order',function($query) use($request){
                $query->where('weight', 'LIKE', "%{$request->search}%")
                ->orWhere('customer_reference', 'LIKE', "%{$request->search}%")
                ->orWhere('corrios_tracking_code', 'LIKE', "%{$request->search}%")
                ->orWhere('warehouse_number', 'LIKE', "%{$request->search}%")
                ->orWhere('tracking_id', 'LIKE', "%{$request->search}%");
                return $query->whereHas('user',function($query) use($request) {
                   return $query->where('name', 'LIKE', "%{$request->search}%");
               });
            })
           ->orWhereHas('user',function($query) use($request) {
            return $query->where('name', 'LIKE', "%{$request->search}%");
        });

        }

        $sales = $query->orderBy('id','desc');

        return $paginate ? $sales->paginate($pageSize) : $sales->get();
    }

}