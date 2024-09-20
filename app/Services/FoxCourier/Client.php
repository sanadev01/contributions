<?php

namespace App\Services\FoxCourier;

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
use App\Services\SwedenPost\Services\Parcel;
use App\Services\Calculators\WeightCalculator;
use App\Services\Correios\Models\PackageError;
use App\Services\SwedenPost\Services\ShippingOrder;

class Client{

    protected $token;
    protected $baseUrl;
    protected $client;

    public function __construct()
    {
        if(app()->isProduction()){
            $this->token = config('fox_courier.production.token');
            $this->baseUrl = config('fox_courier.production.base_uri');
        }else{ 
            $this->token = config('fox_courier.test.token');
            $this->baseUrl = config('fox_courier.test.base_uri');
        }

        $this->client = new GuzzleClient();

    }

    private function getHeaders($type, $path)
    {
        return [ 
            'Authorization' => $this->token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];
    }

    public function createPackage($order)
    {   
        $parcel = new Parcel($order);
        $shippingRequest = $parcel->getRequestBody();

        try {
            $orderURI = 'add-shipment';
            $labelURI = 'print';
            $response = Http::withHeaders($this->getHeaders('POST', $orderURI))->post($this->baseUrl.$orderURI, [$shippingRequest]);

            $data = json_decode($response);
            if($data->status == "Success") {
                $trackingNumber = $data->data[0]->trackingNo;
                $orderId = $data->data[0]->orderId;
                if ($trackingNumber){
                    $labelData = [
                        "orderIds" => ["$trackingNumber"],
                        "labelType" => 1,
                        "packinglist" => true,
                        "merged" => false,
                        "labelFormat" => "PDF",
                    ];
                    $printLabel = Http::withHeaders($this->getHeaders('POST', $labelURI))->post($this->baseUrl.$labelURI, $labelData);

                    $printResponse = json_decode($printLabel);
                    if($printResponse->status == "Failure") {
                        $deleteURI = 'services/shipper/order/'.$orderId;
                        $deleteLabel = Http::withHeaders($this->getHeaders('DELETE', $deleteURI))->DELETE($this->baseUrl.$deleteURI);
                        return new PackageError("Error while printing label. Code: ".$printResponse->errors[0]->code.' Description: '.$printResponse->errors[0]->message);
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

}
