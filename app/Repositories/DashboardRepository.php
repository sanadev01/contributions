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
    
    public function getDashboardStats($startDate=NULL,$endDate=NULL)
    {
        $query = Order::query();
        
        if(Auth::user()->isUser()){
            $query->where('user_id',Auth::id());
        }
        if($startDate!=NULL && $endDate!=NULL){
            $query->whereBetween('order_date', [$startDate,$endDate])->count();
        }
        
        $monthName= \Carbon\Carbon::now()->format('F');
        $today = Carbon::today()->format('Y-m-d');

        $confirmedOrder = $query->where('status', '>=' ,Order::STATUS_PAYMENT_DONE);

        $totalOrders = $query->count();
        $confirmedOrders = $confirmedOrder->count();
        
        //current year orders
        $totalCompleteOrders = $confirmedOrder->count();
        $totalCurrentMonthOrders = $query->whereMonth('order_date', date('m'))->count();
        
        //current month orders
        $totalCompleteOrders = $confirmedOrder->count();
        $totalCurrentMonthOrders = $query->whereMonth('order_date', date('m'))->count();
        
        //today orders
        $totalTodayOrders = $query->where('created_at', 'LIKE', $today.'%')->count();
        $todayDoneOrders = $confirmedOrder->where('created_at', 'LIKE', $today.'%')->count();

        $todayConfirmOrders = $queryconfirmedOrder->count();
        $totalRefundOrder = $query->where('status',Order::STATUS_REFUND)->count();
        
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
    }

}