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
        $monthName= \Carbon\Carbon::now()->format('F');
        $currentyear=\Carbon\Carbon::now()->year;
        $currentmonth=\Carbon\Carbon::now()->month;
        $today = Carbon::today()->format('Y-m-d');
        $currentYearsorders = Order::query();
        $currentMonthorders = Order::query();
        $CurentDay = Order::query();
        $totalOrderQuery = Order::query();
        if($startDate != NULL && $endDate != NULL){
            $currentYearsorders = Order::whereBetween('order_date', [$startDate .' 00:00:00',$endDate.' 23:59:59']);
            $currentMonthorders = Order::whereBetween('order_date', [$startDate .' 00:00:00',$endDate.' 23:59:59']);
            $CurentDay = Order::whereBetween('order_date', [$startDate .' 00:00:00',$endDate.' 23:59:59']);
            $totalOrderQuery = Order::whereBetween('order_date', [$startDate .' 00:00:00',$endDate.' 23:59:59']);
        }
        if(Auth::user()->isUser()){
            $currentYearsorders->where('user_id', Auth::id());
            $currentMonthorders->where('user_id', Auth::id());
            $CurentDay->where('user_id', Auth::id());
            $totalOrderQuery->where('user_id', Auth::id());
        }
        $currentYearTotal  = $currentYearsorders->whereYear('order_date',$currentyear)->count();
        $currentYearConfirm  = $currentYearsorders->where('status', '>=' ,Order::STATUS_PAYMENT_DONE)->count();
        $currentmonthTotal  = $currentMonthorders->whereMonth('order_date',$currentmonth)->whereYear('order_date',$currentyear)->count();
        $currentmonthConfirm  = $currentMonthorders->where('status', '>=' ,Order::STATUS_PAYMENT_DONE)->count();
        $currentDayTotal  = $CurentDay->whereDate('order_date',$today)->count();
        $currentDayConfirm  = $CurentDay->where('status', '>=' ,Order::STATUS_PAYMENT_DONE)->count();
        $totalOrder = $totalOrderQuery->count();
        $totalCompleteOrders =  $totalOrderQuery->where('status', '>=' ,Order::STATUS_PAYMENT_DONE)->count();
        return  $order[] = [
            'totalOrders' => $totalOrder,
            'totalCompleteOrders' =>$totalCompleteOrders,
            'currentmonthTotal'=>$currentmonthTotal,
            'currentmonthConfirm'=>$currentmonthConfirm,
            'currentDayTotal'=>$currentDayTotal,
            'currentDayConfirm'=>$currentDayConfirm,
            'currentYearTotal'=>$currentYearTotal,
            'currentYearConfirm'=>$currentYearConfirm,
            'monthName'=>$monthName
        ];
    }

}