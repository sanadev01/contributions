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
use App\Services\FoxCourier\Services\Parcel;
use App\Services\Calculators\WeightCalculator;
use App\Services\Correios\Models\PackageError;

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
        $orderURI = 'add-shipment';
        dd($shippingRequest);
        // dd($this->baseUrl.$orderURI);

        try {
            $orderURI = 'add-shipment';
            $labelURI = 'print';
            $response = $this->client->post($this->baseUrl.$orderURI, [
                'headers' => ['Authorization' => $this->token],
                'json' => $shippingRequest
            ]);

            $data = json_decode($response->getBody()->getContents());
            dd($data);
            if($data->success) {
                $trackingNumber = $data->reference;

                if ($trackingNumber){
                    $order->update([
                        'corrios_tracking_code' => $trackingNumber,
                        'cn23' => [
                            "tracking_code" => $trackingNumber,
                            "stamp_url" => route('warehouse.cn23.download', $order->id),
                            'leve' => false
                        ],
                        'api_response' => json_encode($data)
                    ]);
                    $this->addOrderTracking($order);

                    //Print Label APi

                    $printLabel = $this->client->get($this->baseUrl.$labelURI."/".$trackingNumber, [
                        'headers' => ['Authorization' => $this->token]
                    ]);

                    $printResponse = json_decode($printLabel->getBody()->getContents());

                    if(!$printResponse->success) {
                        return new PackageError("Error while printing label. ".$printResponse->errors[0]);
                    }
                }
            }
            if(!$data->success) {
                return new PackageError("Error while creating shipment. ".$data->errors[0]);
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
