<?php

namespace App\Repositories;

use App\Models\AffiliateSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AffiliateSaleRepository
{
    public function get(Request $request,$paginate = true,$pageSize=50){
        $query = AffiliateSale::has('user')->with('order');

        if (Auth::user()->isUser()) {
            $query->where('user_id', Auth::id());
        }

        if ( $request->date ){
            $query->where(function($query) use($request){
                return $query->where('created_at', 'LIKE', "%{$request->date}%");
            });
        }

        if ( $request->name ){
            $query->whereHas('user',function($query) use($request) {
                return $query->where('name', 'LIKE', "%{$request->name}%");
            });
        }

        if ( $request->order ){
            $query->where(function($query) use($request){
                return $query->where('order_id', 'LIKE', "%{$request->order}%");
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

        $sales = $query;

        return $paginate ? $sales->paginate($pageSize) : $sales->get();
    }

    public function getSalesForExport($request)
    {   
        $query = AffiliateSale::has('user')->with('order');

        if (Auth::user()->isUser()) {
            $query->where('user_id', Auth::id());
        }

        $startDate = $request->start_date . ' 00:00:00';
        $endDate = $request->end_date.' 23:59:59';
        
        if ( $request->start_date ){
            $query->where('created_at','>', $startDate);
        }
        
        if ( $request->end_date ){
            $query->where('created_at','<=',$endDate);
        }
        
        return $query->get();
    }


}