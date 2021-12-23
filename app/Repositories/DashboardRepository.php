<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\HandlingService;
use App\Models\Order;
use Exception;

class DashboardRepository
{
    
    public function getDashboardStats($startDate=NULL,$endDate=NULL){
        $query = Order::query();
        $monthName= \Carbon\Carbon::now()->format('F');
        
        if(\Auth::user()->isUser()){
            $query->where('user_id',\Auth::user()->id);
        }
        if($startDate!=NULL && $endDate!=NULL){
            $query->whereBetween('order_date', [$startDate,$endDate])->count();
        }
            $totalOrder = $query->count();
            $totalCompleteOrders = $query->where('status', '>=' ,Order::STATUS_PAYMENT_DONE)->count();
            $totalCurrentMonthOrders = $query->whereMonth('order_date', date('m'))->count();
            $CompleteCurrentMonthOrders = $query->whereMonth('order_date', date('m'))->where('status', '>=' ,Order::STATUS_PAYMENT_DONE)->count();
            $totalTodayOrders= $query->whereDate('order_date', date('d-m-y h:i:s'))->count();
            $totalTodayCompletedOrders = $query->whereDate('order_date', date('d-m-y h:i:s'))->where('status', '>=' ,Order::STATUS_PAYMENT_DONE)->count();
            $totalCanceledOrder  = $query->where('status',Order::STATUS_CANCEL)->count();
            $todayConfirmOrders = $query->whereDate('order_date', date('d-m-y h:i:s'))->where('status', '>=' ,Order::STATUS_PAYMENT_DONE)->count();
            $totalRefundOrder = $query->where('status',Order::STATUS_REFUND)->count();
            dd( Order::where('status',Order::STATUS_REFUND)->count());
            return  $order[] = [
                'totalOrders' => $totalOrder,
                'totalCompleteOrders' =>$totalCompleteOrders,
                'totalCurrentMonthOrders'=>$totalCurrentMonthOrders,
                'CompleteCurrentMonthOrders'=>$CompleteCurrentMonthOrders,
                'totalTodayOrders'=>$totalTodayOrders,
                'totalTodayCompletedOrders'=>$totalTodayCompletedOrders,
                'totalCanceledOrder'=>$totalCanceledOrder,
                'todayConfirmOrders'=>$todayConfirmOrders,
                'totalRefundOrder'=>$totalRefundOrder,
                'monthName'=>$monthName
                
            ];
            // dd($order);
    }

}