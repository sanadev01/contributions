<?php

namespace App\Http\Controllers\Admin\Modals;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderInvoiceModalController extends Controller
{
    public function __invoke(Order $order)
    {
        $services = $order->services;

        if( $order->services->filter(function ($service) {return $service->name == 'Insurance';}) &&  $order->user->insurance == false)
        {
           $services = $this->calculateInsurance($order);
        }

        return view('admin.modals.orders.invoice',compact('order', 'services'));
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
