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
    private $brazilTrackingCodes = [];
   
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
        $getTrackings = collect();
        if($orders){
            foreach($orders as $order){
                
                $apiResponse = [];
                if($order->trackings->isNotEmpty()){
                    if($order->trackings->last()->status_code == Order::STATUS_SHIPPED){

                        if($order->recipient->country_id == Order::CHILE ){
                            $response = CorreiosChileTrackingFacade::trackOrder($order->corrios_tracking_code);
                            if($response->status == true && ($response->data != null || $response->data != []) ){
                                $apiResponse = [
                                    'success' => true,
                                    'status' => 200,
                                    'service' => 'Correios_Chile',
                                    'trackings' => $order->trackings,
                                    'api_trackings' => collect($this->reverseTrackings($response->data))->last(),
                                    'order' => $order
                                ];
                            } 
                        }elseif($order->recipient->country_id == Order::US ){
                            if($order->shippingService->service_sub_class == ShippingService::UPS_GROUND){
    
                                $response = UPSFacade::trackOrder($order->corrios_tracking_code);
                                if($response->success == true && !isset($response->data['trackResponse']['shipment'][0]['warnings']))
                                {
                                    $apiResponse = [
                                        'success' => true,
                                        'status' => 200,
                                        'service' => 'UPS',
                                        'trackings' => $order->trackings,
                                        'api_trackings' => collect($this->reverseTrackings($response->data['trackResponse']['shipment'][0]['package'][0]['activity']))->last(),
                                        'order' => $order
                                    ];
    
                                }
                            }
    
                            $response = USPSTrackingFacade::trackOrder($order->corrios_tracking_code);
                            if($response->status == true){
                                $apiResponse = [
                                    'success' => true,
                                    'status' => 200,
                                    'service' => 'USPS',
                                    'trackings' => $order->trackings,
                                    'api_trackings' => collect($this->reverseTrackings($response->data))->last(),
                                    'order' => $order
                                ];
                            }
                        }elseif($order->recipient->country_id == Order::BRAZIL ){
                            if($order->carrier == 'Correios Brazil'){
                                array_push($this->brazilTrackingCodes, $order->corrios_tracking_code);
                            }

                            $apiResponse = [
                                'success' => true,
                                'status' => 200,
                                'service' => 'HD',
                                'trackings' => $order->trackings,
                                'order' => $order
                            ];
                        }else{
                            $apiResponse = [
                                'success' => false,
                                'status' => 201,
                                'service' => 'HD',
                                'trackings' => null,
                                'order' => null
                            ];
                        }
                        $getTrackings->push($apiResponse);
                    }else{
                        $apiResponse = [
                            'success' => true,
                            'status' => 200,
                            'service' => 'HD',
                            'trackings' => $order->trackings,
                            'order' => $order
                        ];
                        $getTrackings->push($apiResponse);
                    }
                }
            }
        }else{
            $apiResponse = [
                'success' => false,
                'status' => 404,
                'service' => 'HD',
                'trackings' => null,
                'order' => null
            ];
            $getTrackings->push($apiResponse);
        }

        if (count($this->brazilTrackingCodes) > 0) {
            $response = CorreiosBrazilTrackingFacade::trackOrder(implode('' ,$this->brazilTrackingCodes));

            if ($response->success == true) {

                $getTrackings = $getTrackings->map(function($item, $key) use ($response){
                    if (count($this->brazilTrackingCodes) > 1) {
                        foreach ($response->data as $data) {
                            if($data->erro ?? false){
                                return $item;
                            }
                            if($item['order']->corrios_tracking_code == $data->numero){
                                $item['api_trackings'] = collect($data->evento);
                                $item['service'] = 'Correios_Brazil';
                            }
                        }
                    }else{
                        if ($response->data->erro ?? false) {
                            return $item;
                        }

                        if ($response->data->numero == $item['order']->corrios_tracking_code) {

                            $item['api_trackings'] = collect($response->data->evento);
                            $item['service'] = 'Correios_Brazil';
                        }
                    }
                    
                    return $item;
                });
            }
        }
        return $getTrackings;
    }

    private function reverseTrackings($response)
    {
        $response = array_reverse($response);

        return $response;
    }

}