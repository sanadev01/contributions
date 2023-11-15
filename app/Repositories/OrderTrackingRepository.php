<?php

namespace App\Repositories;
use App\Models\Order;
use App\Models\Setting;
use App\Facades\UPSFacade;
use App\Models\ShippingService;
use App\Facades\USPSTrackingFacade;
use App\Services\TotalExpress\Client;
use App\Facades\CorreiosChileTrackingFacade;
use App\Facades\CorreiosBrazilTrackingFacade;
use App\Services\SwedenPost\DirectLinkTrackingService;
use App\Http\Resources\TrackingUserResource;
use App\Services\Correios\Services\Brazil\CorreiosTrackingService;
use Illuminate\Support\Facades\Log;

class OrderTrackingRepository
{

    private $trackingNumber;
    private $brazilTrackingCodes = [];
    private $directLinkTrackingCodes = [];
    private $totalExpressTrackingCodes = [];

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
        // dd($trackingNumbers);
        $orders = Order::whereIn('corrios_tracking_code', $trackingNumbers)->orWhereIn('warehouse_number',$trackingNumbers)->orWhereIn('tracking_id',$trackingNumbers)->get();

        $getTrackings = collect();
        if ($orders) {
            foreach ($orders as $key => $order) {
            Log::info('tracking order :found');
            Log::info($order->carrierService());

                $apiResponse = [];
                if ($order->trackings->isNotEmpty() && $order->shippingService != null) {
                    Log::info('tracking order : not empty and shipping not null');
                    if($order->trackings->last()->status_code == Order::STATUS_SHIPPED){

                    if ($order->recipient->country_id == Order::CHILE) {
                     Log::info('tracking order :  recipient country  chile');
                        $response = CorreiosChileTrackingFacade::trackOrder($order->corrios_tracking_code);
                        if ($response->status == true && ($response->data != null || $response->data != [])) {
                            $apiResponse = [
                                'success' => true,
                                'status' => 200,
                                'service' => 'Correios_Chile',
                                'trackings' => $order->trackings,
                                'api_trackings' => collect($this->reverseTrackings($response->data))->last(),
                                'order' => $order
                            ];
                        }
                    } elseif ($order->recipient->country_id == Order::US) {
                        Log::info('tracking order : recipient country US');
                        if ($order->shippingService->service_sub_class == ShippingService::UPS_GROUND) {

                            $response = UPSFacade::trackOrder($order->corrios_tracking_code);
                            if ($response->success == true && !isset($response->data['trackResponse']['shipment'][0]['warnings'])) {
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
                        if ($response->status == true) {
                            $apiResponse = [
                                'success' => true,
                                'status' => 200,
                                'service' => 'USPS',
                                'trackings' => $order->trackings,
                                'api_trackings' => collect($this->reverseTrackings($response->data))->last(),
                                'order' => $order
                            ];
                        }
                    } elseif ($order->recipient->country_id == Order::BRAZIL) {
                     Log::info('tracking order :  recipient country  brazil');
                        
                        if($order->shippingService->is_total_express) {
                             Log::info('tracking order :  recipient shippingService total express');
                            array_push($this->totalExpressTrackingCodes, $order->corrios_tracking_code);
                        }

                        if ($order->carrier == 'Correios AJ' || $order->carrier == 'Correios A' || $order->carrier == 'Correios Brazil' || $order->carrier == 'Global eParcel' || $order->carrier == 'Prime5') {
                            array_push($this->brazilTrackingCodes, $order->corrios_tracking_code);
                        }
                        $apiResponse = [
                            'success' => true,
                            'status' => 200,
                            'service' => 'HD',
                            'trackings' => $order->trackings,
                            'order' => $order
                        ];
                        Log::info('tracking order :    brazil service added');
                    } else {
                        Log::info('tracking order :  not brazil');
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
                        Log::info('tracking order : not SHIPPED');

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
        } else {
            
            Log::info('tracking order : no order');
            $apiResponse = [
                'success' => false,
                'status' => 404,
                'service' => 'HD',
                'trackings' => null,
                'order' => null
            ];
            $getTrackings->push($apiResponse);
        }

        Log::info('tracking order : count '.count($getTrackings));
        
        $serviceClient = new CorreiosTrackingService();
        if (!empty($this->brazilTrackingCodes)) {
            // $response = CorreiosBrazilTrackingFacade::trackOrder(implode('', $this->brazilTrackingCodes));
            if(count($this->brazilTrackingCodes) > 1) {
                    Log::info('tracking order : brazilTrackingCodes>1 ');

                $response = $serviceClient->getMultiTrackings($this->brazilTrackingCodes);
                Log::info('tracking order : response ');
                Log::info([$response]);
                

            } elseif (count($this->brazilTrackingCodes) == 1) {
                $response = $serviceClient->getTracking($this->brazilTrackingCodes[0]);
                Log::info('tracking order : brazilTrackingCodes>1 ');

                $response = $serviceClient->getMultiTrackings($this->brazilTrackingCodes);
                Log::info('tracking order : response ');
                Log::info([$response]);
            }
            if (isset($response->objetos) && is_array($response->objetos) && count($response->objetos) > 0) {
                $getTrackings = $getTrackings->map(function ($item, $key) use ($response) {
                    foreach ($response->objetos as $data) {
                        if (isset($data->erro)) {                            
                            Log::info('tracking order : data->erro ');
                            Log::info([$data->erro]);
                            return $item;
                        }
                        if ($item['order']->corrios_tracking_code == optional($data)->codObjeto) {
                            $item['api_trackings'] = collect(optional($data)->eventos);
                            $item['service'] = 'Correios_Brazil';
                        }
                    }
                    Log::info('tracking order : items 1');
                    Log::info([$item]);
                    return $item;
                });
            }
        }

        if (count($this->directLinkTrackingCodes) > 0) {
            Log::info('tracking order : directLinkTrackingService ');

            $directLinkTrackingService = new DirectLinkTrackingService();
            $response = $directLinkTrackingService->trackOrders($this->directLinkTrackingCodes);
            if ($response->status == true) {
                $getTrackings = $getTrackings->map(function($item, $key) use ($response){
                    if (count($this->directLinkTrackingCodes) > 1) {
                        foreach ($response->data['Item'] as $key=>$data) {
                            
                            Log::info('tracking order : items directLinkTrackingService 0');
                            Log::info([$item]);
                            if($response->status == false){
                                return $item;
                            }
                            if($item['order']->corrios_tracking_code == $data['ItemNumber']){
                                $item['api_trackings'] = collect($this->reverseTrackings($data['Events']))->last();
                                $item['service'] = 'Prime5';
                            }
                        }
                    }else{
                        if($response->status == false){
                            Log::info('tracking order : items directLinkTrackingService 1');
                            Log::info([$item]);
                            return $item;
                        }
                        if ($response->data['Item']['ItemNumber'] == $item['order']->corrios_tracking_code) {

                            $item['api_trackings'] = collect($this->reverseTrackings($response->data['Item']['Events']))->last();
                            $item['service'] = 'Prime5';
                        }
                    }                    
                    Log::info('tracking order : items directLinkTrackingService 2');
                    Log::info([$item]);
                    return $item;
                });
            }
        }
        if (count($this->totalExpressTrackingCodes) > 0) {     
            $totalExpClient = new Client();       
            Log::info('tracking order : totalExpClient ');
            $response = $totalExpClient->getPacketTracking($this->totalExpressTrackingCodes);
            if ($response['success'] == true) {
                $getTrackings = $getTrackings->map(function($item, $key) use ($response){
                    foreach ($response['data'] as $key=>$data) {
                        if($response['success'] == false){
                            
                    Log::info('tracking order : items totalExpClient 1');
                    Log::info([$item]);
                            return $item;
                        }
                        if($item['order']->corrios_tracking_code == $data['trackingNumber']){
                            $item['api_trackings'] = collect($data['Events']);
                            $item['service'] = 'Total Express';
                        }
                    }
                    
                    Log::info('tracking order : items totalExpClient 2');
                    Log::info([$item]);
                    return $item;
                });
            }
        }
              
        Log::info('tracking order :return getTrackings ');
        Log::info([$getTrackings]);
        return $getTrackings;
    }

    private function reverseTrackings($response)
    {
        $response = array_reverse($response);

        return $response;
    }

    public function getTrackings($request) {
        $users = Setting::where('key', 'MARKETPLACE')->where('value', 'AMAZON')->pluck('user_id')->toArray();
        $trackingCodes = Order::whereIn('user_id', $users)
        ->with("user")
        ->where('status', Order::STATUS_SHIPPED)
        ->whereBetween('order_date', [$request->start_date, $request->end_date])
        ->get();
        return TrackingUserResource::collection($trackingCodes);
    }
}
