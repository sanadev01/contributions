<?php

namespace App\Repositories;

use stdClass;
use App\Models\Order;
use App\Facades\UPSFacade;
use App\Models\ShippingService;
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

        $trackingNumbers = explode(',', preg_replace('/\s+/', '', $this->trackingNumber));
        
        $orders = Order::whereIn('corrios_tracking_code', $trackingNumbers)->get();
        
        foreach($orders as $order){
            if($order){
                if($order->trackings->last()->status_code == Order::STATUS_SHIPPED){
                    
                    
                    if($order->recipient->country_id == Order::CHILE && !$order->trackings->isEmpty()){
                        
                        $response = CorreiosChileTrackingFacade::trackOrder($order->corrios_tracking_code);
                        if($response->status == true && ($response->data != null || $response->data != []) ){
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
                    if($order->recipient->country_id == Order::US && !$order->trackings->isEmpty()){

                        if($order->shippingService->service_sub_class == ShippingService::UPS_GROUND){

                            $response = UPSFacade::trackOrder($order->corrios_tracking_code);
                            if($response->success == true && !isset($response->data['trackResponse']['shipment'][0]['warnings']))
                            {
                                return (Object) [
                                    'success' => true,
                                    'status' => 200,
                                    'service' => 'UPS',
                                    'trackings' => $order->trackings,
                                    'ups_trackings' => $this->reverseTrackings($response->data['trackResponse']['shipment'][0]['package'][0]['activity']),
                                    'order' => $order
                                ];
                            }
                        }

                        $response = USPSTrackingFacade::trackOrder($order->corrios_tracking_code);
                        if($response->status == true){
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
        
                    if($order->recipient->country_id == Order::BRAZIL && !$order->trackings->isEmpty()){
                        $response = CorreiosBrazilTrackingFacade::trackOrder($order->corrios_tracking_code);
                        dd($response);
                        if($response->success == true){
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
                        'success' => false,
                        'status' => 201,
                        'service' => 'HD',
                        'trackings' => null,
                        'order' => null
                    ];
    
                }
                return (Object)[
                    'success' => true,
                    'status' => 200,
                    'service' => 'HD',
                    'trackings' => $order->trackings,
                    'order' => $order
                ];
            }
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