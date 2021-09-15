<?php

namespace App\Repositories;

use stdClass;
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
            if($order->recipient->country_id == Order::CHILE && !$order->trackings->isEmpty())
            {
                if($order->trackings->last()->status_code == Order::STATUS_ARRIVE_AT_WAREHOUSE)
                {
                    $response = CorreiosChileTrackingFacade::trackOrder($this->trackingNumber);

                    if($response->status == true)
                    {
                        $trackings = $this->pushToTrackings($response->data, $order->trackings);

                        return (Object) [
                            'success' => true,
                            'status' => 200,
                            'service' => 'Correios_Chile',
                            'trackings' => $trackings,
                        ];
                    }
                   
                }

                return (Object)[
                    'success' => true,
                    'status' => 200,
                    'service' => 'Correios_Chile',
                    'trackings' => $order->trackings,
                ];
            }
            if(!$order->trackings->isEmpty()){
                return (Object)[
                    'success' => true,
                    'status' => 200,
                    'service' => 'Correios_Brazil',
                    'trackings' => $order->trackings,
                ];
            }
            return (Object)[
                'success' => false,
                'status' => 201,
                'service' => 'Correios_Brazil',
                'trackings' => null,
            ];

        }

        return (Object)[
            'success' => false,
            'status' => 404,
            'service' => 'HD',
            'trackings' => null,
        ];
    }

    private function pushToTrackings($response, $hd_trackings)
    {
        
        foreach($response as $data)
        {

            $hd_trackings->push($data);
        }

        return $hd_trackings;
    }

}