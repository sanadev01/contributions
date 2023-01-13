<?php

namespace App\Services\PostPlus;

use App\Models\Order;
use App\Models\OrderTracking;
use Illuminate\Support\Facades\Http;
use App\Models\Warehouse\DeliveryBill;
use GuzzleHttp\Client as GuzzleClient;
use App\Services\PostPlus\Services\Parcel; 
use App\Services\Correios\Contracts\Package;
use App\Services\Correios\Contracts\Container;
use App\Services\Correios\Models\PackageError;

class Client{

    protected $client;

    private $apiKey = 'labelapitest1234567890';
    private $baseUri = 'https://api.test.post-plus.io/api/v1';

    public function __construct()
    {
        $this->client = new GuzzleClient([
            'base_uri' => $this->baseUri
        ]);
    } 

    private function getHeaders()
    {
        return [ 
            'x-api-key' => $this->apiKey,
            'Content-Type' => 'application/json'
        ];
    }
    public function createPackage(Package $order)
    {
        $shippingRequest = (new Parcel())->getRequestBody($order);
        try {
            $response = Http::withHeaders($this->getHeaders())->put('https://api.test.post-plus.io/api/v1/parcels', $shippingRequest);
            $data = json_decode($response);
            //dd($data->errorDetails);
            if($data->status->status == "Created") {
                $trackingNumber = $data->identifiers->parcelNr;
                $printId = $data->prints[0]->id;
                if($trackingNumber && $printId) {
                    $getLabel = Http::withHeaders($this->getHeaders())->get("https://api.test.post-plus.io/api/v1/parcels/parcel-prints/get-many?ids=$printId&IncludeContents=true");
                    $getLabelResponse = json_decode($getLabel);
                    if(!$getLabelResponse->prints[0]->hasErrors) {
                        $order->update([
                            'corrios_tracking_code' => $trackingNumber,
                            'api_response' => json_encode($getLabelResponse),
                            'cn23' => [
                                "tracking_code" => $trackingNumber,
                                "stamp_url" => route('warehouse.cn23.download',$order->id),
                                'leve' => false
                            ],
                        ]);
                        // store order status in order tracking
                        return $this->addOrderTracking($order);
                    }
                    if($getLabelResponse->prints[0]->hasErrors) {
                        return new PackageError("Error while print label. Code: ".$getLabelResponse->prints[0]->hasErrors.' Description: '.$getLabelResponse->prints[0]->hasErrors);
                    }
                }
            }
            if($data->errorDetails) {
                return new PackageError("Error while creating parcel. Code: ".$data->errorDetails[0]->code.' Description: '.$data->errorDetails[0]->detail);
            }
            return null;
        }catch (\Exception $exception){
            return new PackageError($exception->getMessage());
        }
    }

    public function createContainer(Container $container)
    {
 
    }

    public function registerDeliveryBill(DeliveryBill $deliveryBill)
    { 
    }

    public function getDeliveryBillStatus(DeliveryBill $deliveryBill)
    { 
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

    public function destroy($container)
    { 
    }
     

}
