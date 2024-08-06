<?php

namespace App\Services\VipParcel;

use App\Models\Order;
use App\Models\OrderTracking;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client as GuzzleClient;
use App\Services\VipParcel\Parcel;
use App\Services\Correios\Models\PackageError;

class Client{

    protected $baseUrl;
    protected $client;

    public function __construct()
    {
        if(app()->isProduction()){
            $this->baseUrl = config('vipparcel.production.baseUrl');
        }else{ 
            $this->baseUrl = config('vipparcel.test.baseUrl');
        }

        $this->client = new GuzzleClient();

    }

    private function getHeaders()
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];
    }

    public function createPackage($order)
    {
        $parcel = new Parcel($order);
        $shippingRequest = $parcel->getRequestBody();
        // dd($shippingRequest);
        try {
            $orderURI = 'label/print';
            $response = Http::withHeaders($this->getHeaders())->post($this->baseUrl.$orderURI, $shippingRequest);
            $data = json_decode($response);
            if($response->successful()) {

                $trackingNumber = $data->trackNumber;
                $order->update([
                    'corrios_tracking_code' => $trackingNumber,
                    'api_response' => $response,
                    'cn23' => [
                        "tracking_code" => $trackingNumber,
                        "stamp_url" => route('warehouse.cn23.download',$order->id),
                        'leve' => false
                    ],
                ]);
                // store order status in order tracking
                return $this->addOrderTracking($order);
            } else {
                return new PackageError($data->error);
            }
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
