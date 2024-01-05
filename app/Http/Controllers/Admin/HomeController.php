<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
    
}
