<?php

namespace App\Http\Controllers\Admin;


use App\Models\Rate;
use App\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Session;
use App\Repositories\DashboardRepository;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(DashboardRepository $dashboard)
    {
        $orders = $dashboard->getDashboardStats();
        return view('home',compact('orders'));   
    }
    
    public function findContainer(Order $order)
    {
        $container = $order->containers()->first();
        if ($container) {
            dump($container->toArray());

            dump($container->deliveryBills->toArray());
        }
    }

    public function updateArrivalDate()
    {
        $from = '2022-06-01 00:00:00';
        $to = '2022-06-02 00:00:00';
        
        $orders = Order::where('arrived_date', null)->whereHas('trackings', function($query) use($from, $to) {
            $query->where([['status_code', Order::STATUS_ARRIVE_AT_WAREHOUSE], ['created_at', '>=', $from], ['created_at', '<', $to]]);
        })->get();

        if ($orders->isEmpty()) {
            return 'No order found';
        }
        
        foreach ($orders as $order) {
            $order->update([
                'arrived_date' => $order->trackings->where('status_code', 73)->first()->created_at
            ]);
        }

        return 'Done';
    }
}
