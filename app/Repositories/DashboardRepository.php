<?php

namespace App\Repositories;

use Exception;
use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\HandlingService;
use Illuminate\Support\Facades\Auth;

class DashboardRepository
{
    
    public function getDashboardStats($startDate=NULL, $endDate=NULL)
    {
        $carbon       = Carbon::now();
        $monthName    = $carbon->format('F');
        $currentYear  = $carbon->year;
        $currentmonth = $carbon->month;
        $today        = $carbon->format('Y-m-d');
        
        $currentYearsorders = Order::query();
        $currentMonthorders = Order::query();
        $CurentDay          = Order::query();
        $totalOrderQuery    = Order::query();

        if($startDate && $endDate){
            $date = [$startDate .' 00:00:00',$endDate.' 23:59:59'];
            $currentYearsorders = $currentYearsorders->whereBetween('order_date', $date);
            $currentMonthorders = $currentMonthorders->whereBetween('order_date', $date);
            $CurentDay          = $CurentDay->whereBetween('order_date', $date);
            $totalOrderQuery    = $totalOrderQuery->whereBetween('order_date', $date);
        }
        if(Auth::user()->isUser()){
            $authUser = Auth::id();
            $currentYearsorders->where('user_id', $authUser);
            $currentMonthorders->where('user_id', $authUser);
            $CurentDay->where('user_id', $authUser);
            $totalOrderQuery->where('user_id', $authUser);
        }
        
        $paymentDone = Order::STATUS_PAYMENT_DONE;
        $currentYearTotal    = $currentYearsorders->whereYear('order_date',$currentYear)->count();
        $currentYearConfirm  = $currentYearsorders->where('status', '>=' ,$paymentDone)->count();
        $currentmonthTotal   = $currentMonthorders->whereMonth('order_date',$currentmonth)->whereYear('order_date',$currentYear)->count();
        $currentmonthConfirm = $currentMonthorders->where('status', '>=' ,$paymentDone)->count();
        $currentDayTotal     = $CurentDay->whereDate('order_date',$today)->count();
        $currentDayConfirm   = $CurentDay->where('status', '>=' ,$paymentDone)->count();
        $totalOrder          = $totalOrderQuery->count();
        $totalCompleteOrders = $totalOrderQuery->where('status', '>=' ,$paymentDone)->count();

        return  $order[] = [
            'totalOrders'         => $totalOrder,
            'totalCompleteOrders' => $totalCompleteOrders,
            'currentmonthTotal'   => $currentmonthTotal,
            'currentmonthConfirm' => $currentmonthConfirm,
            'currentDayTotal'     => $currentDayTotal,
            'currentDayConfirm'   => $currentDayConfirm,
            'currentYearTotal'    => $currentYearTotal,
            'currentYearConfirm'  => $currentYearConfirm,
            'monthName'           => $monthName
        ];
    }

}