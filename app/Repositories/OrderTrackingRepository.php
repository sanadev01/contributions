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
        
        if( $order != null )
        {
            $this->order = $order;

            return (Object)[
                'success' => true,
                'homedelivery_trackings' => $order->trackings->toArray(),
                'api_tracking' => $this->api_trackings(),
            ];
        }
    }

    public function api_trackings()
    {
        if($this->order->recipient->country_id == Order::BRAZIL)
        {
            $response = CorreiosBrazilTrackingFacade::trackOrder($this->trackingNumber);

            if($response->success == true)
            {
                return $response->data;
            }
        }
        
        if($this->order->recipient->country_id == Order::CHILE)
        {
            $response = CorreiosChileTrackingFacade::trackOrder($this->trackingNumber);

            if($response->success == true)
            {
                return $response->data;
            }
        }

        if($this->order->recipient->country_id == Order::USPS)
        {
            $response = USPSTrackingFacade::trackOrder($this->trackingNumber);

            if($response->success == true)
            {
                return $response->data;
            }
        }
    }
}