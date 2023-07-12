<?php

namespace App\Repositories\Reports;

use App\Models\Order;
use App\Models\ShippingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnjunReportsRepository
{
    protected $error;

    public function get(Request $request, $paginate = true, $pageSize=50)
    {
        $query = Order::has('user')->where('status', '>=', Order::STATUS_PAYMENT_DONE);
        $query->whereHas('shippingService',function($query) {
            return $query->whereIn('service_sub_class', [ShippingService::AJ_Packet_Standard, ShippingService::AJ_Packet_Express]);
        });
        if(Auth::user()->isUser()){
            $query->where('user_id', Auth::id());
        }
        if ( $request->search ){
            $query->whereHas('user',function($query) use($request) {
                return $query->where('name', 'LIKE', "%{$request->search}%");
            });
            $query->orWhereHas('user',function($query) use($request) {
                return $query->where('warehouse_number', 'LIKE', "%{$request->search}%")
                ->orWhere('corrios_tracking_code', 'LIKE', "%{$request->search}%");
            });
        }
        $query->orderBy('id','desc');
        return $paginate ? $query->paginate($pageSize):$query->get();
    }

    public function getAnjunReport($request)
    {
        $orders = Order::has('user')->where('status', '>=', Order::STATUS_PAYMENT_DONE);
        $orders->whereHas('shippingService',function($orders) {
            return $orders->whereIn('service_sub_class', [ShippingService::AJ_Packet_Standard, ShippingService::AJ_Packet_Express]);
        });
        if (Auth::user()->isUser()) {
            $orders->where('user_id', Auth::id());
        }
        $startDate  = $request['start_date'].' 00:00:00';
        $endDate    = $request['end_date'].' 23:59:59';
        if ( $request['start_date'] ){
            $orders->where('order_date','>=',$startDate);
        }
        if ( $request['end_date'] ){
            $orders->where('order_date','<=',$endDate);
        }

        return $orders->orderBy('id')->get();

    }

}
