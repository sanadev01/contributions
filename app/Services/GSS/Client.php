<?php

namespace App\Services\GSS;

use App\Models\Order;
use App\Models\OrderTracking;
use Illuminate\Support\Facades\Http;
use App\Services\GSS\Services\Parcel; 
use App\Models\Warehouse\DeliveryBill;
use GuzzleHttp\Client as GuzzleClient;
use App\Services\Correios\Contracts\Package;
use App\Services\Correios\Contracts\Container;
use App\Services\Correios\Models\PackageError;

class Client{

    protected $userId;
    protected $password;
    protected $locationId;
    protected $workStationId;
    protected $baseUrl;

    public function __construct()
    {   
        if(app()->isProduction()){
            $this->userId = config('gss.production.userId');
            $this->password = config('gss.production.password');
            $this->locationId = config('gss.production.locationId');
            $this->workStationId = config('gss.production.workStationId');
            $this->baseUrl = config('gss.production.baseUrl');
        }else{ 
            $this->userId = config('gss.test.userId');
            $this->password = config('gss.test.password');
            $this->locationId = config('gss.test.locationId');
            $this->workStationId = config('gss.test.workStationId');
            $this->baseUrl = config('gss.test.baseUrl');
        }

        $this->client = new GuzzleClient();
    } 

    private function getHeaders()
    {
        $authParams = [
            'userId' => $this->userId,
            'password' => $this->password,
            'locationId' => $this->locationId,
            'workStationId' => $this->workStationId,
        ];
        $response = $this->client->post("$this->baseUrl/Authentication/login",['json' => $authParams ]);
        $data = json_decode($response->getBody()->getContents());
        if($data->accessToken) {
            return [ 
                'Authorization' => "Bearer {$data->accessToken}",
                'Accept' => 'application/json'
            ];
        }
    }
    public function createPackage(Package $order)
    {
        $shippingRequest = (new Parcel())->getRequestBody($order);
        try {
            $request = Http::withHeaders($this->getHeaders())->post("$this->baseUrl/Package/LabelAndProcessPackage", $shippingRequest);
            $response = json_decode($request);
            if($response->success) {
                $order->update([
                    'corrios_tracking_code' => $response->trackingNumber,
                    'api_response' => json_encode($response),
                    'cn23' => [
                        "tracking_code" => $response->trackingNumber,
                        "stamp_url" => route('warehouse.cn23.download',$order->id),
                        'leve' => false
                    ],
                ]);
                // store order status in order tracking
                return $this->addOrderTracking($order);
            }
            else {
                return new PackageError("Error while creating parcel. Code".$response->statusCode.". Description: ".$response->message);
            }
            return null;
        }catch (\Exception $exception){
            return new PackageError($exception->getMessage());
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
