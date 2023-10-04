<?php

namespace App\Repositories;

use Exception;
use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\HandlingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardRepository
{

    public function getDashboardStats($startDate = NULL, $endDate = NULL)
    {
        $carbon       = Carbon::now();
        $monthName    = $carbon->format('F');
        $currentYear  = $carbon->year;
        $currentMonth = $carbon->month;
        $today        = $carbon->format('Y-m-d');
        $isUser = Auth::user()->isUser();
 
        if ($startDate && $endDate) {
            $date = [ $startDate . ' 00:00:00', $endDate . ' 23:59:59'];
        }
        else{
            $date = [$carbon->firstOfMonth().'', ($carbon->lastOfMonth()->addDays(1)).''];
        }  
              


        //total order
        $totalReport = Order::when($isUser,function($query)  {
            return $query->where('user_id',Auth::id());
        })
        ->selectRaw('is_paid, COUNT(*) as count')
        ->groupBy('is_paid')
        ->get(); 
        try{ 
            $totalOrder = $totalReport[0]->count??0;
        }catch(\Exception $e){
            $totalOrder = 0;
        }
        try{
            $totalCompleteOrders = optional($totalReport[1])->count??0;
        }catch(\Exception $e){
            $totalCompleteOrders = 0;
        }
        $totalOrder+=$totalCompleteOrders;
        //currentDayReport
        $currentDayReport = Order::whereDate('order_date', $today)
        ->whereBetween('order_date', $date)
        ->when($isUser,function($query)  {
            return $query->where('user_id',Auth::id());
        })
        ->selectRaw('is_paid, COUNT(*) as count')
        ->groupBy('is_paid')
        ->get(); 
        try{ 
            $currentDayTotal = optional($currentDayReport[0])->count??0;
        }catch(\Exception $e){
            $currentDayTotal = 0;
        }
        try{
            $currentDayConfirm = optional($currentDayReport[1])->count??0;
        }catch(\Exception $e){
            $currentDayConfirm = 0;
        }
        $currentDayTotal+=$currentDayConfirm; 
        $currentMonthReport = Order::whereMonth('order_date', $currentMonth)
        ->whereYear('order_date', $currentYear) 
        ->when($isUser,function($query)  {
            return $query->where('user_id',Auth::id());
        })
        ->selectRaw('is_paid, COUNT(*) as count')
        ->groupBy('is_paid')
        ->get(); 
        try{ 
            $currentMonthTotal = $currentMonthReport[0]->count;
        }catch(\Exception $e){
            $currentMonthTotal = 0;
        }
        try{
            $currentMonthConfirm = $currentMonthReport[1]->count;
        }catch(\Exception $e){
            $currentMonthConfirm = 0;
        }
        $currentMonthTotal+=$currentMonthConfirm;
        //currentYearReport
        $currentYearReport = Order::whereYear('order_date', $currentYear) 
        ->when($isUser,function($query)  {
            return $query->where('user_id',Auth::id());
        })
        ->selectRaw('is_paid, COUNT(*) as count')
        ->groupBy('is_paid')
        ->get(); 
        try{ 
            $currentYearTotal = $currentYearReport[0]->count??0;
        }catch(\Exception $e){
            $currentYearTotal = 0;
        }
        try{
            $currentYearConfirm = optional($currentYearReport[1])->count??0;
        }catch(\Exception $e){
            $currentYearConfirm = 0;
        }
        $currentYearTotal+=  $currentYearConfirm;



        //bar chart
        $lastDayOfCurrentMonth = Carbon::parse(Carbon::now())->endOfMonth();
        $lastTwelveMonths = Carbon::parse(Carbon::now())->endOfMonth()->subMonths(12);

        $totalOrderByMonth = Order::whereBetween('created_at', [$lastTwelveMonths, $lastDayOfCurrentMonth])->orderBy('created_at', 'asc')->selectRaw('id,created_at')
        ->get()
        ->groupBy(function ($val) {
            return Carbon::parse($val->created_at)->format('Y-M');
        })->map(function ($groupedItems) {
            return $groupedItems->count();
        });


        $totalShippedOrder = Order::where('status', Order::STATUS_SHIPPED)->whereBetween('created_at', [$lastTwelveMonths, Carbon::now()])->selectRaw('id,created_at')
        ->get() 
        ->groupBy(function ($val) {
            return Carbon::parse($val->created_at)->format('Y-M');
        })->map(function ($groupedItems) {
            return $groupedItems->count();
        });
        
        $months = array_map(function ($key, $value) {
            return $key ;
        }, array_keys($totalOrderByMonth->toArray()), $totalOrderByMonth->toArray()); 
        $totalOrderCount =    array_values($totalOrderByMonth->toArray()); 
        $totalShippedCount =   array_values($totalShippedOrder->toArray());
 

        // doughnut chart started
        $newValue = $currentYearTotal;
        $oldValue= Order::whereBetween('created_at', [Carbon::now()->subMonths(24), $lastTwelveMonths])
        ->when($isUser,function($query)  {
            return $query->where('user_id',Auth::id());
        })
        ->selectRaw('id') 
        ->count(); 
        $percentIncreaseThisYear = number_format(((($newValue - $oldValue) / $oldValue) * 100), 2);
 
        
        $newValue = end($totalOrderCount); // Get the last element
        $oldValue = prev($totalOrderCount); // Get the second-to-last element 
        $percentIncreaseThisMonth = number_format(((($newValue - $oldValue) / $oldValue) * 100), 2);

        // chart 
        $statusCounts = Order::selectRaw('status, COUNT(*) as count')
        ->whereIn('status', [
            Order::STATUS_SHIPPED,
            Order::STATUS_PAYMENT_DONE, 
            Order::STATUS_CANCEL,
            Order::STATUS_REFUND
        ])
        ->groupBy('status')
        ->get();

        $statusCounts = $statusCounts->pluck('count', 'status');

        // Now you can access the counts for each status like this:
        $shippedOrderCount = $statusCounts[Order::STATUS_SHIPPED] ?? 0;
        $doneOrderCount = $statusCounts[Order::STATUS_PAYMENT_DONE] ?? 0; 
        $cancelledOrderCount = $statusCounts[Order::STATUS_CANCEL] ?? 0;
        $refundOrderCount = $statusCounts[Order::STATUS_REFUND] ?? 0;


        //doughnut chart end
        return ([
            'totalOrders'         => $totalOrder,
            'totalCompleteOrders' => $totalCompleteOrders,
            'currentMonthTotal'   => $currentMonthTotal,
            'currentMonthConfirm' => $currentMonthConfirm,
            'currentDayTotal'     => $currentDayTotal,
            'currentDayConfirm'   => $currentDayConfirm,
            'currentYearTotal'    => $currentYearTotal,
            'currentYearConfirm'  => $currentYearConfirm,
            'monthName'           => $monthName,
            'totalShippedCount'   => $totalShippedCount,
            'totalOrderCount'     => $totalOrderCount,
            'months'              => $months,
            'percentIncreaseThisMonth' => $percentIncreaseThisMonth,
            'percentIncreaseThisYear'  => $percentIncreaseThisYear,
            'doughnutData' =>
            [
                ['x'=> "Shipped", 'value'=> $shippedOrderCount],
                ['x'=> "Done", 'value'=>  $doneOrderCount],
                ['x'=> "Refund/Cancelled", 'value'=>    $cancelledOrderCount+$refundOrderCount]
            
 
            ]

        ]);
    }
}
