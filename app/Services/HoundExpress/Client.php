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
use App\Services\SwedenPost\Services\ShippingOrder;

class Client{

    //Sweden Post Parameters
    protected $secret;
    protected $token;
    protected $host;
    protected $baseUrl;
    //Sweden Post Parameters End
    protected $client;

    public function __construct()
    {
        if(app()->isProduction()){
            $this->partnerKey = config('prime5.production.partner_key'); 
        }else{ 
            $this->partnerKey = config('prime5.test.partner_key');
        }

        $this->client = new GuzzleClient();

    }

    private function getHeaders($type, $path) 
    {
        return [
            'partnerKey' => $this->partnerKey, 
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];
    }

    public function createPackage($order)
    {
        dd(3);
        $shippingRequest = (new ShippingOrder($order))->getRequestBody();
        try {
            $path = 'services/shipper/orderLabels';
            $shipmentClose = 'services/shipper/closeShipments';
            $response = Http::withHeaders($this->getHeaders('POST', $path))->post($this->baseUrl.$path, $shippingRequest);
            $data = json_decode($response);
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
