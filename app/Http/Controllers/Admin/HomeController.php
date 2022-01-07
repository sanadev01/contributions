<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Rate;
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
        $rates = Rate::where('shipping_service_id', 20)->get();
        foreach ($rates as $rate) {
            $rate->delete();
        }
        $orders = $dashboard->getDashboardStats();
        
        return view('home',compact('orders'));   
    }
    
    public function test()
    {
        return true;
    }
}
