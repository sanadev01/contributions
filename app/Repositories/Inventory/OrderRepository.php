<?php

namespace App\Repositories\Inventory;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderRepository
{

    public function getOdersForExport($request)
    {
        $orders = Order::where('status','>=',Order::STATUS_ORDER)->has('products');
        
        if (Auth::user()->isUser()) {
            $orders->where('user_id', Auth::id());
        }
        $startDate  = $request->start_date.' 00:00:00';
        $endDate    = $request->end_date.' 23:59:59';
        
        if ( $request->start_date ){
            $orders->where('order_date','>=',$startDate);
        }
        if ( $request->end_date ){
            $orders->where('order_date','<=',$endDate);
        }
        
        return $orders->orderBy('id')->get();
    }
}
