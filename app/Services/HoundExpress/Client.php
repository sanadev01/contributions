<?php

namespace App\Services\HoundExpress;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderTracking;
use App\Models\ShippingService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\Warehouse\DeliveryBill;
use GuzzleHttp\Client as GuzzleClient;
use App\Services\Converters\UnitsConverter;
use App\Services\Calculators\WeightCalculator;
use App\Services\Correios\Models\PackageError;
use App\Services\HoundExpress\Services\CN23\HoundErrorHandler;
use App\Services\HoundExpress\Services\CN23\HoundOrder;
class Client{

    //Sweden Post Parameters 
    private $baseUrl;
    private $partnerKey;

    public function __construct()
    {
        if(app()->isProduction()){
            $this->partnerKey = config('hound.production.partner_key'); 
            $this->baseUrl = config('hound.production.base_url');
        }else{ 
            $this->partnerKey = config('hound.test.partner_key');
            $this->baseUrl = config('hound.test.base_url'); 

        }
        $this->client = new GuzzleClient();

    }

    private function getHeaders() 
    {
        return [
            'partnerKey' => $this->partnerKey, 
        ];
    }

    public function createPackage($order)
    { 
        $houndOrderRequest = (new HoundOrder($order))->getRequestBody();   
        try {
            
            $response = Http::withHeaders($this->getHeaders())->post($this->baseUrl.'/Sabueso/ws/deliveryServices/createOrder',$houndOrderRequest);
            $response_body = json_decode($response->getBody());
            $error = (new HoundErrorHandler($response_body))->getError();
            if($error){
                return new PackageError($error);
               
            }
            else{
                return new PackageError('no error');
            }

            if($data->status == "Success") {
                $trackingNumber = $data->data[0]->trackingNo;
                if ($trackingNumber){
                    $closeShipment = Http::withHeaders($this->getHeaders('POST', $shipmentClose))->post($this->baseUrl.$shipmentClose, ['shipmentIds' => [$trackingNumber]]);
                    $closeShipmentResponse = json_decode($closeShipment);
                    if($closeShipmentResponse->status == "Success") {
                        $order->update([
                            'corrios_tracking_code' => $trackingNumber,
                            'api_response' => json_encode($data),
                            'cn23' => [
                                "tracking_code" => $trackingNumber,
                                "stamp_url" => route('warehouse.cn23.download',$order->id),
                                'leve' => false
                            ],
                        ]);
                        // store order status in order tracking
                        return $this->addOrderTracking($order);
                    }
                    if($closeShipmentResponse->status == "Failure") {
                        return new PackageError("Error while closing Shipment. Code: ".$closeShipmentResponse->errors[0]->code.' Description: '.$closeShipmentResponse->errors[0]->message);
                    }
                }
            }
            if($data->status == "Failure") {
                return new PackageError("Error while creating shipment. Code: ".$data->errors[0]->code.' Description: '.$data->errors[0]->message);
            }
            return null;
        }catch (\GuzzleHttp\Exception\ClientException $e) {
            return new PackageError($e->getResponse()->getBody()->getContents());
        }
    }

    public function addOrderTracking($order)
    {
        if($order->trackings->isEmpty())
        {
            OrderTracking::create([
                'order_id' => $order->id,
                'status_code' => Order::STATUS_PAYMENT_DONE,
                'type' => 'HD',
                'description' => 'Order Placed',
                'country' => ($order->user->country != null) ? $order->user->country->code : 'US',
                'city' => 'Miami',
            ]);
        }

        return true;
    }

    public function deleteOrder($orderId)
    {
        try {
            $path = "http://qa.etowertech.com/services/shipper/order/{$orderId}";
            $response = Http::withHeaders($this->getHeaders("DELETE", $path))->delete($this->baseUrl.$path);
            $data = json_decode($response);
            if ($data->status == "Failure") {
                return [
                    'success' => false,
                    'message' => "Error while shipment cancellation. Code: ".$data->errors[0]->code.' Description: '.$data->errors[0]->message,
                    'data' => null
                ];
            }
            return [
                'success' => true,
                'data' => $data
            ];
        }catch (\Exception $exception){
            return new PackageError($exception->getMessage());
        }
    }

}
