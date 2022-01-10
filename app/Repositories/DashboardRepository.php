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
        $filter = false;

        if($startDate != NULL && $endDate != NULL){
            $filter = true;
            $filterQuery = Order::whereBetween('order_date', [$startDate .' 00:00:00',$endDate.' 23:59:59']);
        }

        if(Auth::user()->isUser()){

            $query = ($filter == true) ? $filterQuery->where('user_id', Auth::id()) : Order::where('user_id', Auth::id());

        }else{

            $query = ($filter == true) ? $filterQuery : Order::query();
        }

        $monthName= \Carbon\Carbon::now()->format('F');
        $currentyear=\Carbon\Carbon::now()->year;
        $currentmonth=\Carbon\Carbon::now()->month;
        $today = Carbon::today()->format('Y-m-d');

        
        $totalOrder = $query->count();
        $Curentyear =  $query;
        $currentYearTotal  = $Curentyear->whereYear('order_date',$currentyear)->count();
        $currentYearConfirm  = $Curentyear->where('status', '>=' ,Order::STATUS_PAYMENT_DONE)->count();
        
        if(Auth::user()->isUser()){
            $CurentMonth = ($filter == true) ? $filterQuery->where('user_id', Auth::id()) : Order::where('user_id',Auth::id());
        }else{
            $CurentMonth =   ($filter == true) ? $filterQuery : Order::query();
        }
        $currentmonthTotal  = $CurentMonth->whereMonth('order_date',$currentmonth)->whereYear('order_date',$currentyear)->count();
        $currentmonthConfirm  = $CurentMonth->where('status', '>=' ,Order::STATUS_PAYMENT_DONE)->whereYear('order_date',$currentyear)->count();

        if(Auth::user()->isUser()){
            $CurentDay = ($filter == true) ? $filterQuery->where('user_id', Auth::id()) : Order::where('user_id',Auth::id());
        }else{
            $CurentDay =   ($filter == true) ?  $filterQuery : Order::query();
        }

        $currentDayTotal  = $CurentDay->whereDate('order_date',$today)->count();
        
        $currentDayConfirm  = $CurentDay->where('status', '>=' ,Order::STATUS_PAYMENT_DONE)->count();
        if(Auth::user()->isUser()){
            $totalCompleteOrders  = ($filter == true)  ? Order::where('user_id',Auth::id())->whereBetween('order_date', [$startDate .' 00:00:00',$endDate.' 23:59:59'])->where('status', '>=' ,Order::STATUS_PAYMENT_DONE)->count() : Order::where('user_id',Auth::id())->where('status', '>=' ,Order::STATUS_PAYMENT_DONE)->count();
        }else{
            $totalCompleteOrders  = ($filter == true)  ? Order::whereBetween('order_date', [$startDate .' 00:00:00',$endDate.' 23:59:59'])->where('status', '>=' ,Order::STATUS_PAYMENT_DONE)->count() : Order::where('status', '>=' ,Order::STATUS_PAYMENT_DONE)->count();
        } 
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