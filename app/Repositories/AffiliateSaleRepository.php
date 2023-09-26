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
            $query->where('user_id', Auth::id());
            if($request->user_id){
                $query->where('referrer_id', $request->user_id);
            }
        }
        if ($request->orderIds) {
              $query->whereIn('id', json_decode($request->orderIds));
        }
        if(Auth::user()->isAdmin() && $request->user_id){
            $query->where('user_id', $request->user_id);
        }
        if ( $request->status == 'paid' ){
            $query->where('is_paid', true);
        }
        
        if ( $request->status == 'unpaid' ){
            $query->where('is_paid',false);
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
        
        if($request->status == 'downlaod'){
            return $query->get()->sortByDesc('order.user_id');
        }

        if ( $request->name ){
            $query->whereHas('user',function($query) use($request) {
                return $query->where('name', 'LIKE', "%{$request->name}%");
            });
        }
        if ( $request->user ){
             $query->whereHas('user',function($query) use($request) {
                   return $query->where('name', 'LIKE', "%{$request->user}%");
               });
        }

        if ( $request->order ){
            $query->where(function($query) use($request){
                return $query->where('order_id', 'LIKE', "%{$request->order}%");
            });
        }
       
        if ( $request->whr ){
            $query->whereHas('order',function($query) use($request){
                return $query->where('warehouse_number', 'LIKE', "%{$request->whr}%");
            });
        }
        if ( $request->corrios_tracking ){
            $query->whereHas('order',function($query) use($request){
                return $query->where('corrios_tracking_code', 'LIKE', "%{$request->corrios_tracking}%");
            });
        }
        if ( $request->reference ){
            $query->whereHas('order',function($query) use($request){
                return $query->where('customer_reference', 'LIKE', "%{$request->reference}%");
            });
        }

        if ( $request->tracking ){
            $query->whereHas('order',function($query) use($request){
                return $query->where('tracking_id', 'LIKE', "%{$request->tracking}%");
            });
        }
        if ( $request->weight ){
            $query->whereHas('order',function($query) use($request){
                return $query->where('weight', 'LIKE', "%{$request->weight}%");
            });
        }

        if ( $request->value ){
            $query->where(function($query) use($request){
                return $query->where('value',"{$request->value}");
            });
        }

        if ( $request->saleType ){
            $query->where(function($query) use($request){
                return $query->where('type',"$request->saleType");
            });
        }

        if ( $request->commission ){
            $query->where(function($query) use($request){
                return $query->where('commission', 'LIKE', "%{$request->commission}%");
            });
        }

        $sales = $query->orderBy('id','desc');

        return $paginate ? $sales->paginate($pageSize) : $sales->get();
    }

    public function getSalesForExport($request)
    {   
        $query = AffiliateSale::has('user')->with('order')->has('order');
        
        if (Auth::user()->isUser()) {
            $query->where('user_id', Auth::id());
        }
        
        // if ($request->user_id) {
        //     $query->where('user_id', $request->user_id);
        // }

        $startDate = $request->start_date . ' 00:00:00';
        $endDate = $request->end_date.' 23:59:59';
        
        if ( $request->start_date ){
            $query->where('created_at','>', $startDate);
        }
        
        if ( $request->end_date ){
            $query->where('created_at','<=',$endDate);
        }
        
        if ( $request->status == 'paid' ){
            $query->where('is_paid', true);
        }
        
        if ( $request->status == 'unpaid' ){
            $query->where('is_paid',false);
        }
        
        return $query->get()->sortByDesc('order.user_id');
    }


}