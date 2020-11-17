<?php

namespace App\Repositories\Reports;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderReportsRepository
{
    protected $error;

    public function getShipmentReportOfUsers(Request $request,$paginate = true,$pageSize=50,$orderBy = 'id',$orderType='asc')
    {
        $query = User::query();
            $query->with(['orders']);

        if ( $request->user ){
            $query->where('name','LIKE',"%{$request->user}%")
                    ->orWhere('last_name','LIKE',"%{$request->user}%")
                    ->orWhere('pobox_number','LIKE',"%{$request->user}%")
                    ->orWhere('email','LIKE',"%{$request->user}%");
        }

        $query->withCount(['orders as order_count'=> function($query) use ($request){
            
            if ( $request->start_date ){
                $query->where('order_date','>',$request->start_date);
            }

            if ( $request->end_date ){
                $query->where('order_date','<=',$request->end_date);
            }

            $query->where('status','>',Order::STATUS_PAYMENT_PENDING);

        },'orders as weight' => function($query) use ($request) {

            if ( $request->start_date ){
                $query->where('order_date','>',$request->start_date);
            }

            if ( $request->end_date ){
                $query->where('order_date','<=',$request->end_date);
            }

            $query->select(DB::raw('sum(CASE WHEN measurement_unit = "kg/cm" THEN weight ELSE (weight/2.205) END) as weight'));

            $query->where('status','>',Order::STATUS_PAYMENT_PENDING); 

        },'orders as spent' => function($query) use ($request) {
            if ( $request->start_date ){
                $query->where('order_date','>',$request->start_date);
            }

            if ( $request->end_date ){
                $query->where('order_date','<=',$request->end_date);
            }

            $query->select(DB::raw('sum(gross_total) as spent'));

            $query->where('status','>',Order::STATUS_PAYMENT_PENDING);
        }])
        ->orderBy($orderBy,$orderType);

        return $paginate ? $query->paginate($pageSize) : $query->get();
    }

    public function ordersReportsdownloads()
    {
        $orders = Order::where('status','>=',Order::STATUS_ORDER)
        ->has('user')->get();
        if (Auth::user()->isUser()) {
            $orders->where('user_id', Auth::id())->get();
        }
        return $orders;
    }
}
