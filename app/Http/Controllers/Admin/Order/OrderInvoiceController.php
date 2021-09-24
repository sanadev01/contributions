<?php

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderInvoiceController extends Controller
{
    public function index(Request $request, Order $order)
    {
        $this->authorize('viewInvoice',$order);
        
        if ( !$order->recipient || $order->items->isEmpty() ){
            abort(404);
        }
        
        $services = $order->services;
        if(($order->user->hasRole('wholesale') && $order->user->insurance == false) || $order->user->hasRole('retailer'))
        {

            if( $order->services->filter(function ($service) {return $service->name == 'Insurance';}) &&  $order->user->insurance == false)
            {
               $services = $this->calculateInsurance($order);
            }
        }
        
        return view('admin.orders.invoice.index',compact('order', 'services'));
    }

    public function store(Request $request, Order $order)
    {

    }

    private function calculateInsurance($order)
    {
        foreach ($order->services as $service) 
        {
            if($service->name == 'Insurance' || $service->name == 'Seguro')
            {

                $total_insurance = (3/100) * $order->order_value;

                if ($total_insurance > 35) 
                {
                    $service->price = $total_insurance;
                }
            }
        }

        return $order->services;
    }
}
