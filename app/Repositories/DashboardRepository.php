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
        $orderShipped = Order::STATUS_SHIPPED;
        $orderPaymentDone = Order::STATUS_PAYMENT_DONE;
        $orderCancel = Order::STATUS_CANCEL;
        $orderRefund = Order::STATUS_REFUND;
        
        $carbon       = Carbon::now();
        $monthName    = $carbon->format('F');
        $currentYear  = $carbon->year;
        $currentMonth = $carbon->month;
        $today        = $carbon->format('Y-m-d');
        $isUser = Auth::user()->isUser();


        $date = ($startDate && $endDate) ? [$startDate . ' 00:00:00', $endDate . ' 23:59:59'] : [$carbon->firstOfMonth() . '', ($carbon->lastOfMonth()->addDays(1)) . ''];

        //total order
        $totalReport = Order::when($isUser, function ($query) {
            return $query->where('user_id', Auth::id());
        })->selectRaw('is_paid, COUNT(*) as count')
            ->groupBy('is_paid')
            ->get();
        $totalCompleteOrders = (isset($totalReport[1]) ? $totalReport[1]->count : 0); 
        $totalOrder = (isset($totalReport[0]) ? $totalReport[0]->count : 0) + $totalCompleteOrders; 
     
         
        //currentDayReport
        $currentDayReport = Order::whereDate('order_date', $today)
            ->whereBetween('order_date', $date)
            ->when($isUser, function ($query) {
                return $query->where('user_id', Auth::id());
            })
            ->selectRaw('is_paid, COUNT(*) as count')
            ->groupBy('is_paid')
            ->get();
            
        $currentDayConfirm = (isset($currentDayReport[1]) ? $currentDayReport[1]->count : 0); 
        $currentDayTotal = (isset($currentDayReport[0]) ? $currentDayReport[0]->count : 0) + $currentDayConfirm; 

        $currentMonthReport = Order::whereMonth('order_date', $currentMonth)
            ->whereYear('order_date', $currentYear)
            ->when($isUser, function ($query) {
                return $query->where('user_id', Auth::id());
            })
            ->selectRaw('is_paid, COUNT(*) as count')
            ->groupBy('is_paid')
            ->get();
            
        $currentMonthConfirm = (isset($currentMonthReport[1]) ? $currentMonthReport[1]->count : 0); 
        $currentMonthTotal = (isset($currentMonthReport[0]) ? $currentMonthReport[0]->count : 0) + $currentMonthConfirm; 
   
        //currentYearReport
        $currentYearReport = Order::whereYear('order_date', $currentYear)
            ->when($isUser, function ($query) {
                return $query->where('user_id', Auth::id());
            })
            ->selectRaw('is_paid, COUNT(*) as count')
            ->groupBy('is_paid')
            ->get();
            
        $currentYearConfirm = (isset($currentYearReport[1]) ? $currentYearReport[1]->count : 0); 
        $currentYearTotal = (isset($currentYearReport[0]) ? $currentYearReport[0]->count : 0) + $currentYearConfirm; 
   
  
        $lastTwelveMonths = Carbon::parse(Carbon::now())->endOfMonth()->subMonths(12);

        $totalOrderByMonth = Order::when($isUser, function ($query) {
                return $query->where('user_id', Auth::id());
            })->whereBetween('created_at', [$lastTwelveMonths, Carbon::now()])->orderBy('created_at', 'asc')->selectRaw('id,created_at')
            ->get()
            ->groupBy(function ($val) {
                return Carbon::parse($val->created_at)->format('Y-M');
            })->map(function ($groupedItems) {
                return $groupedItems->count();
            });


        $totalShippedOrder = Order::when($isUser, function ($query) {
            return $query->where('user_id', Auth::id());
        })->where('status', $orderShipped)->whereBetween('created_at', [$lastTwelveMonths, Carbon::now()])->orderBy('created_at', 'asc')->selectRaw('id,created_at')
            ->get()
            ->groupBy(function ($val) {
                return Carbon::parse($val->created_at)->format('Y-M');
            })->map(function ($groupedItems) {
                return $groupedItems->count();
            });

        $months = array_map(function ($key, $value) {
            return $key;
        }, array_keys($totalOrderByMonth->toArray()), $totalOrderByMonth->toArray());
        $totalOrderCount =    array_values($totalOrderByMonth->toArray());
        $totalShippedCount =   array_values($totalShippedOrder->toArray());


        // doughnut chart started
        $newValue = $currentYearTotal;
        $oldValue = Order::when($isUser, function ($query) {
            return $query->where('user_id', Auth::id());
        })->whereBetween('created_at', [Carbon::now()->subMonths(24), $lastTwelveMonths])
            ->when($isUser, function ($query) {
                return $query->where('user_id', Auth::id());
            })
            ->selectRaw('id')
            ->count();
            
        $oldValue = 0;
        $newValue = 0;
        $percentIncreaseThisYear = 0;  
        $percentIncreaseThisMonth = 0;
        $newValue = end($totalOrderCount);
        $oldValue = prev($totalOrderCount);
        
        if($oldValue){
            $percentIncreaseThisMonth = (number_format(((($newValue - $oldValue) / $oldValue) * 100), 2));
            $percentIncreaseThisYear = (number_format(((($newValue - $oldValue) / $oldValue) * 100), 2)); 
        }        

        // chart 
        $statusCounts = Order::when($isUser, function ($query) {
                return $query->where('user_id', Auth::id());
            })->selectRaw('status, COUNT(*) as count')
            ->whereIn('status', [
                $orderShipped,
                $orderPaymentDone,
                $orderCancel,
                $orderRefund
            ])
            ->groupBy('status')
            ->get();

        $statusCounts = $statusCounts->pluck('count', 'status');

        // Now you can access the counts for each status like this:
        $shippedOrderCount = $statusCounts[$orderShipped] ?? 0;
        $doneOrderCount = $statusCounts[$orderPaymentDone] ?? 0;
        $cancelledOrderCount = $statusCounts[$orderCancel] ?? 0;
        $refundOrderCount = $statusCounts[$orderRefund] ?? 0;


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
                ['x' => "Shipped", 'value' => $shippedOrderCount],
                ['x' => "Done", 'value' =>  $doneOrderCount],
                ['x' => "Refund/Cancelled", 'value' =>    $cancelledOrderCount + $refundOrderCount]


            ]

        ]);
    }
}
