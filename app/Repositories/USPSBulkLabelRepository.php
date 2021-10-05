<?php


namespace App\Repositories;


use App\Models\User;
use App\Models\Order;
use App\Facades\USPSFacade;
use App\Models\OrderTracking;
use App\Models\ShippingService;
use Illuminate\Support\Facades\Auth;
use App\Services\USPS\USPSLabelMaker;
use App\Services\USPS\USPSShippingService;


class USPSBulkLabelRepository
{
    private $total_weight = 0;
    private $total_width = 0.0;
    private $total_height = 0.0;
    private $total_length = 0.0;
    private $orders;

    public function handle($order_Ids)
    {
        $this->getOrdersWeight($order_Ids);
        $order = $this->makeOrder();

        return $order;
    }

    private function getOrdersWeight($order_Ids)
    {
        $this->orders = Order::whereIn('id', $order_Ids)->get();

        foreach ($this->orders as $order) {
            $this->total_weight += $order->getWeight();
            $this->total_width += $order->width;
            $this->total_height += $order->height;
            $this->total_length += $order->length;
        }

        return;
    }

    private function makeOrder()
    {
        $order = new Order();
        $order->id = 1;
        $order->user = Auth::user();
        $order->sender_country_id = 250;
        $order->weight = $this->total_weight;
        $order->width = $this->total_width;
        $order->height = $this->total_height;
        $order->length = $this->total_length;
        
        return $order;
    }

}