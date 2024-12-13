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
use App\Services\HoundExpress\Client as HoundClient;
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
                $apiResponse = [];
                if ($order->trackings->isNotEmpty() && $order->shippingService != null) {
                    if($order->trackings->last()->status_code == Order::STATUS_SHIPPED){
                        if ($order->shippingService->is_hound_express){
                            $response = HoundClient::orderTrackings($order->corrios_tracking_code); 
                            
                            if(isset($response['resultDetails'])){
                                $apiResponse = [
                                    'success' => true,
                                    'status' => 200,
                                    'service' => 'Hound Express',
                                    'trackings' => $order->trackings,
                                    'api_trackings' => collect($response['resultDetails']),
                                    'order' => $order
                                ];
                            }else{
                                $apiResponse = [
                                    'success' => false,
                                    'status' => 201,
                                    'service' => 'Hound Express',
                                    'trackings' => null,
                                    'order' => null
                                ];
                            }
                        }elseif ($order->recipient->country_id == Order::CHILE) {

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
                        
                            if($order->shippingService->is_total_express) {
                                array_push($this->totalExpressTrackingCodes, $order->corrios_tracking_code);
                            }

                            if ($order->carrier == 'Correios Brazil' || $order->carrier == 'Global eParcel' || $order->carrier == 'Prime5') {
                                array_push($this->brazilTrackingCodes, $order->corrios_tracking_code);
                            }
                            $apiResponse = [
                                'success' => true,
                                'status' => 200,
                                'service' => 'HD',
                                'trackings' => $order->trackings,
                                'order' => $order
                            ];
                        
                        } elseif ($order->recipient->country_id == Order::Guatemala) {

                            $apiResponse = [
                                'success' => true,
                                'status' => 200,
                                'service' => 'HD',
                                'trackings' => $order->trackings,
                                'order' => $order
                            ];
                        
                        }else {
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
        } else {
            
            $apiResponse = [
                'success' => false,
                'status' => 404,
                'service' => 'HD',
                'trackings' => null,
                'order' => null
            ];
            $getTrackings->push($apiResponse);
        }
        
        $serviceClient = new CorreiosTrackingService();
        if (!empty($this->brazilTrackingCodes)) {
            // $response = CorreiosBrazilTrackingFacade::trackOrder(implode('', $this->brazilTrackingCodes));
            if(count($this->brazilTrackingCodes) > 1) {

                $response = $serviceClient->getMultiTrackings($this->brazilTrackingCodes);

            } elseif (count($this->brazilTrackingCodes) == 1) {
                $response = $serviceClient->getTracking($this->brazilTrackingCodes[0]);

                $response = $serviceClient->getMultiTrackings($this->brazilTrackingCodes);

            }
            if (isset($response->objetos) && is_array($response->objetos) && count($response->objetos) > 0) {
                $getTrackings = $getTrackings->map(function ($item, $key) use ($response) {
                    foreach ($response->objetos as $data) {
                        if (isset($data->erro)) {

                            return $item;
                        }
                        if ($item['order']->corrios_tracking_code == optional($data)->codObjeto) {
                            $item['api_trackings'] = collect(optional($data)->eventos);
                            $item['service'] = 'Correios_Brazil';
                        }
                    }

                    return $item;
                });
            }
        }

        if (count($this->directLinkTrackingCodes) > 0) {

            $directLinkTrackingService = new DirectLinkTrackingService();
            $response = $directLinkTrackingService->trackOrders($this->directLinkTrackingCodes);
            if ($response->status == true) {
                $getTrackings = $getTrackings->map(function($item, $key) use ($response){
                    if (count($this->directLinkTrackingCodes) > 1) {
                        foreach ($response->data['Item'] as $key=>$data) {
                            
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
                            return $item;
                        }
                        if ($response->data['Item']['ItemNumber'] == $item['order']->corrios_tracking_code) {

                            $item['api_trackings'] = collect($this->reverseTrackings($response->data['Item']['Events']))->last();
                            $item['service'] = 'Prime5';
                        }
                    }
                    return $item;
                });
            }
        }
        if (count($this->totalExpressTrackingCodes) > 0) {     
            $totalExpClient = new Client();       
            $response = $totalExpClient->getPacketTracking($this->totalExpressTrackingCodes);
            if ($response['success'] == true) {
                $getTrackings = $getTrackings->map(function($item, $key) use ($response){
                    foreach ($response['data'] as $key=>$data) {
                        if($response['success'] == false){
                            return $item;
                        }
                        if($item['order']->corrios_tracking_code == $data['trackingNumber']){
                            $item['api_trackings'] = collect($data['Events']);
                            $item['service'] = 'Total Express';
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
