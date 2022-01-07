<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;

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
        
        $this->test();
        return view('home',compact('orders'));   
    }
    
    public function test()
    {
        
        try {
            Artisan::call('db:seed --class=RegionSeeder');
            Artisan::call('db:seed --class=CommuneSeeder');

            return true;
        } catch (\Exception $ex) {
            dd($ex->getMessage());
        }
        
    }
}
