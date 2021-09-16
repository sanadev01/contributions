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
                            'order' => $order
                        ];
                    }
                   
                }

                // $trackings = $this->testPush($order->trackings);
                // dd($trackings);
                return (Object)[
                    'success' => true,
                    'status' => 200,
                    'service' => 'HD',
                    'trackings' => $order->trackings,
                    'order' => $order
                ];
            }
            if(!$order->trackings->isEmpty()){
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

    private function testPush($hd_trackings)
    {
        $data = [
            'FechaDate' => '2020-05-30T04:40:00',
            'Fecha' => '30-05-2020 04:40',
            'Estado' => 'PENDIENTE DE ENTREGA. CONTACTAR SERVICIO AL CLIENTE CORREOSCHILE',
            'Oficina' => 'CORREOS CHILE',
            'Icono' => '007',
            'Orden' => 10,
        ];

        $hd_trackings->push($data);

        return $hd_trackings;

    }

}