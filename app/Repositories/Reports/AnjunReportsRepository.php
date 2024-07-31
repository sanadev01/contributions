<?php

namespace App\Repositories\Reports;

use App\Models\Order;
use App\Models\ShippingService;
use App\Models\Warehouse\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Warehouse\DeliveryBill;

class AnjunReportsRepository
{
    protected $error;

    public function get(Request $request, $paginate = true, $pageSize=50)
    {
        $query = Order::has('user')->where('status', '>=', Order::STATUS_PAYMENT_DONE);
        $query->whereHas('shippingService',function($query) use($request){
            if($request->type=="bcn")
                return $query->whereIn('service_sub_class', [ShippingService::BCN_Packet_Standard, ShippingService::BCN_Packet_Express]);
            if($request->type=="anjun")
                return $query->whereIn('service_sub_class', [ShippingService::AJ_Packet_Standard, ShippingService::AJ_Packet_Express]);
            if($request->type=="anjun_china")
                return $query->whereIn('service_sub_class', [ShippingService::AJ_Standard_CN, ShippingService::AJ_Express_CN]);
            if($request->type=="correios")
                return $query->whereIn('service_sub_class', [ShippingService::Packet_Standard,ShippingService::Packet_Express,ShippingService::Packet_Mini]);
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

    public function getAnjunReport($request, $user)
    {
        $query = DeliveryBill::query();
        $query->whereHas('containers', function ($query) use ($request) {
            if($request->type=="bcn")
                return $query->whereIn('services_subclass_code', ['BCN-NX', 'BCN-IX']);
            if($request->type=="anjun")
                return $query->whereIn('services_subclass_code', ["AJ-IX","AJ-NX"]);
            if($request->type=="anjun_china")
                return $query->whereIn('services_subclass_code', ["AJC-IX","AJC-NX"]);
            if($request->type=="correios")
                return $query->whereIn('services_subclass_code', ["IX","NX","XP"]);
        });
        
        $startDate  = $request['start_date'].' 00:00:00';
        $endDate    = $request['end_date'].' 23:59:59';

        if ( $request['start_date'] ){
            $query->where('created_at','>=',$startDate);
        }
        if ( $request['end_date'] ){
            $query->where('created_at','<=',$endDate);
        }

        return $query->get();
    }
    public function getAnjunChinaContainersReport($request)
    {
        $query = Container::with('orders')->whereIn('services_subclass_code', ["AJC-IX","AJC-NX"]);
        $startDate  = $request['start_date'].' 00:00:00';
        $endDate    = $request['end_date'].' 23:59:59';
        if ( $request['start_date'] ){
            $query->where('created_at','>=',$startDate);
        }
        if ( $request['end_date'] ){
            $query->where('created_at','<=',$endDate);
        }
        return $query->get();
    }

}
