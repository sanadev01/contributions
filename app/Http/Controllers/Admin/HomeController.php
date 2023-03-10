<?php

namespace App\Http\Controllers\Admin;


use App\Models\Rate;
use App\Models\Order;
use App\Http\Controllers\Controller;
use App\Models\Warehouse\Container;
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
    
    public function findContainer(Container $container)
    {
        
        if ($container) {
            dump($container->toArray());

            foreach($container->orders as $order){
                echo $order->id ."=>" . $order->shippingService->name . "=>" . $order->corrios_tracking_code . "<br>";
            }
            
        }
    }
}
