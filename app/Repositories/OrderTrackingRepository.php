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
                if($order->trackings->last()->status_code == Order::STATUS_SHIPPED)
                {
                    $response = CorreiosChileTrackingFacade::trackOrder($this->trackingNumber);

                    if($response->status == true && ($response->data != null || $response->data != []) )
                    {
                        // $trackings = $this->pushToTrackings($response->data, $order->trackings);

                        return (Object) [
                            'success' => true,
                            'status' => 200,
                            'service' => 'Correios_Chile',
                            'trackings' => $order->trackings,
                            'chile_trackings' => $this->reverseTrackings($response->data),
                            'order' => $order
                        ];
                    }
                   
                }

                return (Object)[
                    'success' => true,
                    'status' => 200,
                    'service' => 'HD',
                    'trackings' => $order->trackings,
                    'order' => $order
                ];
            }
            if($order->recipient->country_id == Order::US && !$order->trackings->isEmpty())
            {
                if($order->trackings->last()->status_code == Order::STATUS_SHIPPED)
                {
                    $response = USPSTrackingFacade::trackOrder($this->trackingNumber);

                    if($response->status == true)
                    {
                        return (Object) [
                            'success' => true,
                            'status' => 200,
                            'service' => 'USPS',
                            'trackings' => $order->trackings,
                            'usps_trackings' => $this->reverseTrackings($response->data),
                            'order' => $order
                        ];
                    }
                }

                return (Object)[
                    'success' => true,
                    'status' => 200,
                    'service' => 'HD',
                    'trackings' => $order->trackings,
                    'order' => $order
                ];
            }

            if($order->recipient->country_id == Order::BRAZIL && !$order->trackings->isEmpty())
            {
                if($order->trackings->last()->status_code == Order::STATUS_SHIPPED)
                {
                    $response = CorreiosBrazilTrackingFacade::trackOrder($this->trackingNumber);
                    
                    if($response->success == true)
                    {
                        return (Object) [
                            'success' => true,
                            'status' => 200,
                            'service' => 'Correios_Brazil',
                            'trackings' => $order->trackings,
                            'brazil_trackings' => $response->data,
                            'order' => $order
                        ];
                    }
                }

                return (Object)[
                    'success' => true,
                    'status' => 200,
                    'service' => 'HD',
                    'trackings' => $order->trackings,
                    'order' => $order
                ];
            }
            return (Object)[
                'success' => false,
                'status' => 201,
                'service' => 'HD',
                'trackings' => null,
                'order' => null
            ];

        }

        return (Object)[
            'success' => false,
            'status' => 404,
            'service' => 'HD',
            'trackings' => null,
            'order' => null
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

    private function reverseTrackings($response)
    {
        $response = array_reverse($response);

        return $response;
    }

}