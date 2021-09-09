<?php

namespace App\Http\Controllers\Admin;


use App\Models\User;
use App\Models\Order;
use App\Facades\USPSFacade;
use Illuminate\Http\Request;
use App\Models\OrderTracking;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        if ( !Session::has('last_logged_in') ){
            $user = Auth::user();
            if ($user->isUser() && $user->status == 'suspended') {
                Auth::logout();

                session()->flash('alert-danger','Your Account has been suspended Please contact Us / Sua conta foi suspensa Entre em contato conosco');
                return redirect()->route('login');
            }
        }

        return view('home');   
    }

    public function trackings()
    {
        $orders = Order::findMany([2775, 2772, 2771, 2770, 2769, 2768,]);

        foreach ($orders as $order) {
            $this->addOrderTracking($order);
        }

        dd(true);
    }

    public function addOrderTracking($order)
    {
        if($order->trackings->isEmpty())
        {
            OrderTracking::create([
                'order_id' => $order->id,
                'status_code' => $order->status,
                'type' => 'HD',
                'description' => 'Order Placed',
                'country' => $order->recipient->country->name,
                'created_at' => $order->updated_at,
                'updated_at' => $order->updated_at,
            ]);
        }    

        return true;
    }
    
}
