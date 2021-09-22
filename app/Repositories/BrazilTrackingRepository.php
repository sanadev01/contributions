<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderTracking;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Facades\CorreiosBrazilTrackingFacade;


class BrazilTrackingRepository
{

    public $orders = [];

    public function handle()
    {
       return $this->getOrders();
    }

    public function getOrders()
    {
        $orders = Order::where([
                            ['status', Order::STATUS_SHIPPED],
                            ['api_tracking_status', '!=', Order::STATUS_BRAZIL_POSTED],
                            ['api_tracking_status', '!=', null]
                        ])->get();

        foreach ($orders as $order) 
        {

            if($order->recipient->country_id == Order::BRAZIL)
            {
                $response = CorreiosBrazilTrackingFacade::trackOrder($order->corrios_tracking_code);

                if($response->success == true)
                {
                    $this->addOrderTracking($order, $response->data);
                    $order->update(['api_tracking_status' => $response->data->status]);
                }
            }
        }
        
        return true;
    }

    public function addOrderTracking($order, $correios_brazil_response)
    {
        // $date_time = $correios_brazil_response->data. ' ' . $correios_brazil_response->hora;
        // $date_time = str_ireplace('/', '-', $date_time);

        OrderTracking::create([
            'order_id' => $order->id,
            'status_code' => $correios_brazil_response->status,
            'type' => $correios_brazil_response->tipo,
            'description' => $correios_brazil_response->descricao,
            'country' => 'Brazil',
            'city' => $correios_brazil_response->cidade,
        ]);

        return true;
    }

    
}