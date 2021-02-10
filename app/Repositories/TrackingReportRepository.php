<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Http\Request;

class TrackingReportRepository
{
    public function get(Request $request)
    {
        $query = Order::query()
            ->where('status','>=',Order::STATUS_ORDER)
            ->where(function($query){
                $query->where('is_paid',true)
                    ->orWhereHas('paymentInvoices');
            });

        if ( $request->start_date ){
            $query->where('order_date','>=', $request->start_date);
        }

        if ( $request->end_date ){
            $query->where('order_date','<=', $request->end_date);
        }

        return $query->get();
    }
}
