<?php

namespace App\Services\PostPlus;

use App\Models\Order;
use App\Models\OrderTracking;
use App\Models\Warehouse\DeliveryBill;
use GuzzleHttp\Client as GuzzleClient;
use App\Services\Correios\Contracts\Package;
use App\Services\Correios\Contracts\Container;
use App\Services\Correios\Models\PackageError;
use App\Services\PostPlus\Services\Parcel; 
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

    public function createPackage(Package $order)
    {
        try {
            $response = $this->client->post('/parcels',[
                'headers' => [
                    'x-api-key' => $this->apiKey,
                    'Content-Type' => 'application/json'
                ],
                'json' => (new Parcel($order))->getRequest(),
            ]);
return dd($response);
            $data = json_decode($response->getBody()->getContents());
            return $data->requestId;
        }catch (\GuzzleHttp\Exception\ClientException $e) {
            return new PackageError($e->getResponse()->getBody()->getContents());
        }
        catch (\Exception $exception){
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
