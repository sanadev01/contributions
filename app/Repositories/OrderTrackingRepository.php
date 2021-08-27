<?php

namespace App\Repositories;

use App\Models\Order;


class OrderTrackingRepository
{

    private $trackingNumber;
   
    public function __construct($trackingNumber)
    {
        $this->trackingNumber = $trackingNumber;
    }

    public function handle()
    {
       return $this->searchOrder();
    }

    public function searchOrder()
    {
        $order = Order::where('corrios_tracking_code', $this->trackingNumber)->first();
       
        if( $order != null )
        {
            return (Object)[
                'success' => true,
                'trackingresponse' => $order->trackings->toArray(),
            ];
        }
    }
}