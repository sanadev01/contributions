<?php

namespace App\Repositories\Reports;

use App\Models\Order;
use App\Models\ShippingService;
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

        if ( $request->name ){
            $query->where('name','LIKE',"%{$request->name}%")
                    ->orWhere('last_name','LIKE',"%{$request->name}%");
        } elseif ( $request->pobox_number ) 
        {
            $query->where('pobox_number','LIKE',"%{$request->pobox_number}%");
        } elseif ( $request->email)
        {
            $query->where('email','LIKE',"%{$request->email}%");

        } else if( $request->search )
        {
        
            $query->where('name','LIKE',"%{$request->search}%")
            ->orWhere('last_name','LIKE',"%{$request->search}%")
            ->orWhere('pobox_number','LIKE',"%{$request->search}%")
            ->orWhere('email','LIKE',"%{$request->search}%");
        }

        $query->withCount(['orders as order_count'=> function($query) use ($request){
            
            if ( $request->start_date) {
                $startDate = $request->start_date.' 00:00:00';
                $query->where('order_date','>=', $startDate);
            }
            if ($request->end_date ) {
                $endDate = $request->end_date.' 23:59:59';
                $query->where('order_date','<=', $endDate);
            }

            $query->where('status','>',Order::STATUS_PAYMENT_PENDING);

        },'orders as weight' => function($query) use ($request) {

            if ( $request->start_date) {
                $startDate = $request->start_date.' 00:00:00';
                $query->where('order_date','>=', $startDate);
            }
            if ($request->end_date ) {
                $endDate = $request->end_date.' 23:59:59';
                $query->where('order_date','<=', $endDate);
            }

            

            $query->select(DB::raw('sum(CASE WHEN measurement_unit = "kg/cm" THEN ROUND(weight,2) ELSE ROUND((weight/2.205),2) END) as weight'));

            $query->where('status','>',Order::STATUS_PAYMENT_PENDING); 

        },'orders as spent' => function($query) use ($request) {
            if ( $request->start_date) {
                $startDate = $request->start_date.' 00:00:00';
                $query->where('order_date','>=', $startDate);
            }
            if ($request->end_date ) {
                $endDate = $request->end_date.' 23:59:59';
                $query->where('order_date','<=', $endDate);
            }

            $query->select(DB::raw('sum(gross_total) as spent'));

            $query->where('status','>',Order::STATUS_PAYMENT_PENDING);
        }])
        ->orderBy($orderBy,$orderType);

        return $paginate ? $query->paginate($pageSize) : $query->get();
    }

    public function getOrderReport()
    {
        $orders = Order::where('status','>',Order::STATUS_PAYMENT_PENDING)
        ->has('user');
        if (Auth::user()->isUser()) {
            $orders->where('user_id', Auth::id());
        }
        return $orders->get();
    }
   
    public function getShipmentReportOfUsersByWeight($id, $month=null, Request $request)
    {
        $query = Order::where('status','>',Order::STATUS_PAYMENT_PENDING);
        if($id){
            $query->has('user')->where('user_id', $id);
        }
        if( $request->user_id){
            $query->has('user')->where('user_id', $request->user_id);
        }
        if($month){
            $month = date("m",strtotime($month));
            $firatDateOfMonth = $request->year.'-'.$month.'-01';
            $lastDateOfMonth = \Carbon\Carbon::parse($firatDateOfMonth)->endOfMonth()->toDateString();
            $startDate = $firatDateOfMonth.' 00:00:00'; 
            $endDate = $lastDateOfMonth.' 23:59:59';
            $query->whereBetween('order_date', [$startDate,$endDate]);
        }
        if($request->has('start_date')){
            $startDate = $request->start_date.' 00:00:00';
            $query->where('order_date', '>=', $startDate);
        }
        if($request->has('end_date')){
            $endDate = $request->end_date.' 23:59:59';
            $query->where('order_date', '<=', $endDate);
        }
        
        $query->select(DB::raw('CASE WHEN measurement_unit = "kg/cm" THEN ROUND(weight,2) ELSE ROUND((weight/2.205),2) END as kgweight'));
        $record = collect();
        $orders = $query->get();
        
        foreach($this->getWeight() as $weight){
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
                'min_weight' => '0.00',
                'max_weight' => '0.100'
            ],
            [
                'min_weight' => '0.101',
                'max_weight' => '0.200'
            ],
            [
                'min_weight' => '0.201',
                'max_weight' => '0.300'
            ],
            [
                'min_weight' => '0.301',
                'max_weight' => '0.400'
            ],
            [
                'min_weight' => '0.401',
                'max_weight' => '0.500'
            ],
            [
                'min_weight' => '0.501',
                'max_weight' => '0.600'
            ],
            [
                'min_weight' => '0.601',
                'max_weight' => '0.700'
            ],
            [
                'min_weight' => '0.701',
                'max_weight' => '0.800'
            ],
            [
                'min_weight' => '0.801',
                'max_weight' => '0.900'
            ],
            [
                'min_weight' => '0.901',
                'max_weight' => '1.00'
            ],
            [
                'min_weight' => '1.01',
                'max_weight' => '2.00'
            ],
            [
                'min_weight' => '2.01',
                'max_weight' => '3.00'
            ],
            [
                'min_weight' => '3.01',
                'max_weight' => '4.00'
            ],
            [
                'min_weight' => '4.01',
                'max_weight' => '5.00'
            ],
            [
                'min_weight' => '5.01',
                'max_weight' => '6.00'
            ],
            [
                'min_weight' => '6.01',
                'max_weight' => '7.00'
            ],
            [
                'min_weight' => '7.01',
                'max_weight' => '8.00'
            ],
            [
                'min_weight' => '8.01',
                'max_weight' => '9.00'
            ],
            [
                'min_weight' => '9.01',
                'max_weight' => '10.00'
            ],
            [
                'min_weight' => '10.01',
                'max_weight' => '15.00'
            ],
            [
                'min_weight' => '15.01',
                'max_weight' => '20.00'
            ],
            [
                'min_weight' => '20.01',
                'max_weight' => '25.00'
            ],
            [
                'min_weight' => '25.01',
                'max_weight' => '30.00'
            ],
            
        ];

    }

    public function getShipmentReportOfUsersByMonth(Request $request)
    {
        $query = Order::where('status','>',Order::STATUS_PAYMENT_PENDING);
        if($request->user_id){
            $query->where('user_id',$request->user_id);
        }
        $ordersByYear = $query->selectRaw(
            "count(*) as total, Month(order_date) as month, 
            sum(gross_total) as spent,
            sum(CASE WHEN measurement_unit = 'kg/cm' THEN ROUND(weight,2) ELSE ROUND((weight/2.205),2) END) as weight"
        )->groupBy('month')->where('order_date', 'like', "$request->year%" )->orderBy('month','asc')->get();
        return $ordersByYear;
    }

    public function orderReportByService(User $user, $request)
    {
        $correios = [
            ShippingService::Packet_Standard,
            ShippingService::Packet_Express,
            ShippingService::Packet_Mini,
            ShippingService::AJ_Packet_Standard,
            ShippingService::AJ_Packet_Express
        ];
        $chile = [
            ShippingService::SRP,
            ShippingService::SRM,
            ShippingService::Courier_Express
        ];
        $ups = [
            ShippingService::UPS_GROUND
        ];
        $fedex = [
            ShippingService::FEDEX_GROUND
        ];
        $usps = [
            ShippingService::USPS_PRIORITY,
            ShippingService::USPS_FIRSTCLASS,
            ShippingService::USPS_PRIORITY_INTERNATIONAL,
            ShippingService::USPS_FIRSTCLASS_INTERNATIONAL
        ];
        $gps = [
               ShippingService::GePS
        ];
        
        $allServices = array_merge($correios, $chile, $ups, $fedex, $usps, $gps);

        $query = User::query();
        $query->where('id', $user->id);
        
        $query->withCount(['orders as brazil_order_count'=> function($query) use ($correios,$request){

            $query->where('status','>',Order::STATUS_PAYMENT_PENDING);
            $query = $this->requestFilterDate($query,$request);

            $query->whereHas('shippingService', function ($query) use ($correios){
                $query->whereIn('service_sub_class',$correios);
            });

        },'orders as chile_order_count' => function($query) use ($chile,$request){

            $query->where('status','>',Order::STATUS_PAYMENT_PENDING);
            $query = $this->requestFilterDate($query,$request);

            $query->whereHas('shippingService', function ($query) use ($chile){
                $query->whereIn('service_sub_class',$chile);
            });

        },'orders as ups_order_count' => function($query) use ($ups,$request){

            $query->where('status','>',Order::STATUS_PAYMENT_PENDING);
            $query = $this->requestFilterDate($query,$request);

            $query->whereHas('shippingService', function ($query) use ($ups){
                $query->whereIn('service_sub_class',$ups);
            });

        },'orders as usps_order_count' => function($query) use ($usps,$request){

            $query->where('status','>',Order::STATUS_PAYMENT_PENDING);
            $query = $this->requestFilterDate($query,$request);

            $query->whereHas('shippingService', function ($query) use ($usps){
                $query->whereIn('service_sub_class',$usps);
            });

        },'orders as fedex_order_count' => function($query) use ($fedex,$request){

            $query->where('status','>',Order::STATUS_PAYMENT_PENDING);
            $query = $this->requestFilterDate($query,$request);

            $query->whereHas('shippingService', function ($query) use ($fedex){
                $query->whereIn('service_sub_class',$fedex);
            });
        },'orders as gps_order_count' => function($query)  use ($gps,$request){

            $query->where('status','>',Order::STATUS_PAYMENT_PENDING);
            $query = $this->requestFilterDate($query,$request);

            $query->whereHas('shippingService', function ($query) use ($gps){
                $query->whereIn('service_sub_class',$gps);
            });
        },'orders as other_order_count' => function($query)  use ($allServices,$request){

            $query->where('status','>',Order::STATUS_PAYMENT_PENDING);
            $query = $this->requestFilterDate($query,$request);

            $query->whereHas('shippingService', function ($query) use ($allServices){
                $query->whereNotIn('service_sub_class',$allServices);
            });
            
        }]);
        $user = $query->first();
        return $user;
    }

    public function requestFilterDate($query, $request){
        if ( $request->start_date) {
            $startDate = $request->start_date.' 00:00:00';
            $query->where('order_date','>=', $startDate);
        }
        if ($request->end_date ) {
            $endDate = $request->end_date.' 23:59:59';
            $query->where('order_date','<=', $endDate);
        }
        return $query;
    }
}
