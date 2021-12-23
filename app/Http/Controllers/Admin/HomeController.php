<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use App\Facades\CorreiosChileTrackingFacade;
use App\Facades\CorreiosBrazilTrackingFacade;
use App\Models\Warehouse\Container;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        return view('home');   
    }
    
    public function testBrazilTracking($dispatch_number)
    {
        $containers = Container::where('dispatch_number', $dispatch_number)->get();
        dd($containers->toArray());
    }
}
