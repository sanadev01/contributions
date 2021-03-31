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

    public function getOrderReport()
    {
        $orders = Order::where('status','>=',Order::STATUS_PAYMENT_PENDING)
        ->has('user')->get();
        if (Auth::user()->isUser()) {
            $orders->where('user_id', Auth::id())->get();
        }
        return $orders;
    }
   
    public function getShipmentReportOfUsersByWeight(Request $request)
    {
        $query = Order::where('status','>=',Order::STATUS_PAYMENT_PENDING)
        ->has('user')->where('user_id', $request->id);
        
        $query->select(DB::raw('CASE WHEN measurement_unit = "kg/cm" THEN weight ELSE (weight/2.205) END as kgweight'));
        $record = collect();
        foreach($this->getWeight() as $weight){
            $orders = $query->get();
            $ordersCount = $orders->whereBetween('kgweight', [$weight['min_weight'], $weight['max_weight']]);
            
            $record->push([
                'orders' => $ordersCount->count(),
                'min_weight' => $weight['min_weight'],
                'max_weight' => $weight['max_weight'],
            ]);
        }

        return $record;
    }
    
     public function getWeight(){
        return [
            [
                'min_weight' => '0',
                'max_weight' => '1'
            ],
            [
                'min_weight' => '1.01',
                'max_weight' => '2'
            ],
            [
                'min_weight' => '2.01',
                'max_weight' => '3'
            ],
            [
                'min_weight' => '3.01',
                'max_weight' => '4'
            ],
            [
                'min_weight' => '4.01',
                'max_weight' => '5'
            ],
            [
                'min_weight' => '5.01',
                'max_weight' => '6'
            ],
            [
                'min_weight' => '6.01',
                'max_weight' => '7'
            ],
            [
                'min_weight' => '7.01',
                'max_weight' => '8'
            ],
            [
                'min_weight' => '8.01',
                'max_weight' => '9'
            ],
            [
                'min_weight' => '9.01',
                'max_weight' => '10'
            ],
            [
                'min_weight' => '11',
                'max_weight' => '15'
            ],
            [
                'min_weight' => '15.01',
                'max_weight' => '20'
            ],
            [
                'min_weight' => '21',
                'max_weight' => '25'
            ],
            [
                'min_weight' => '25.01',
                'max_weight' => '30'
            ],
            
        ];

     }
}
