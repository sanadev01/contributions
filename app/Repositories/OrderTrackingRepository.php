<?php

namespace App\Repositories;

use App\Models\Order;
use App\Facades\USPSTrackingFacade;
use App\Facades\CorreiosChileTrackingFacade;
use App\Facades\CorreiosBrazilTrackingFacade;


class OrderTrackingRepository
{

    private $trackingNumber;
    private $order;
   
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
        
        if($order){
            if(!$order->trackings->isEmpty()){
                return (Object)[
                    'success' => true,
                    'status' => 200,
                    'trackings' => $order->trackings,
                ];
            }
            return (Object)[
                'success' => false,
                'status' => 201,
                'trackings' => null,
            ];

        }

        return (Object)[
            'success' => false,
            'status' => 404,
            'trackings' => null,
        ];
    }

}