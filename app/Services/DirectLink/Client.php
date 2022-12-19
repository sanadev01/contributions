<?php

namespace App\Services\DirectLink;

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
use App\Services\DirectLink\Services\ShippingOrder;

class Client{

    //direct link parameters
    protected $base_url;
    protected $orderUrl;
    protected $labelUrl; 
    protected $host; 
    protected $token;
    protected $date_time;
    //direct link parameters end

    protected $client;
    
    protected $chargableWeight;

    public function __construct()
    {
        if(app()->isProduction()){
            $this->base_url = config('direct_link.production.base_url');
            $this->orderUrl = config('direct_link.production.orderUrl');
            $this->labelUrl = config('direct_link.production.labelUrl');
            $this->token = config('direct_link.production.token');
            $this->host = config('direct_link.production.host');
            $this->date_time = config('direct_link.production.date_time');
        }else{ 
            
            $this->base_url = config('direct_link.test.base_url');
            $this->orderUrl = config('direct_link.test.orderUrl');
            $this->labelUrl = config('direct_link.test.labelUrl');
            $this->token = config('direct_link.test.token');
            $this->host = config('direct_link.test.host');
            $this->date_time = config('direct_link.test.date_time');
        }

        $this->client = new GuzzleClient(['base_uri' => $this->base_url]);

    }

    private function getHeader()
    {
        return [
            'Host' => "qa.etowertech.com",
            'X-WallTech-Date '=> "Fri, 16 Dec 2022 18:38:45 GMT",
            'Authorization' => "WallTech testa0wXdbpML6JGQ7NRP3O:yWUH6sTKY3tDqfRMhnNZIWIVY6c=",
            'Content-Type' => "application/json",
            'Accept' => "application/json",
        ]; 
    }

    public function returnCurl($headers, $url, $resquestBody) {

    }

    public function createPackage($order)
    {   
        $shippingRequest = (new ShippingOrder())->getRequestBody($order); 
        try {
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://qa.etowertech.com/services/shipper/orders',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>json_encode($shippingRequest),
            CURLOPT_HTTPHEADER => array(
                'Host: qa.etowertech.com',
                'Authorization: WallTech testa0wXdbpML6JGQ7NRP3O:vfxTydw2K3U5CZ6WgQdGwzpKH7Y=',
                'X-WallTech-Date: Mon, 19 Dec 2022 12:17:54 GMT',
                'Accept: application/json',
                'Content-Type: application/json'
            ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $data = json_decode($response);
            
            if($data->status == "Success") {
                $trackingNumber = $data->data[0]->trackingNo;
                if ($trackingNumber){
                    $labelRequest = ['orderIds' => [$trackingNumber], 'labelType' => 1, 'packingList' => false, 'merged' => false, 'labelFormat' => "PDF"]; 
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                    CURLOPT_URL => 'http://qa.etowertech.com/services/shipper/labels',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS =>json_encode($labelRequest),
                    CURLOPT_HTTPHEADER => array(
                        'Host: qa.etowertech.com',
                        'Authorization: WallTech testa0wXdbpML6JGQ7NRP3O:UKedLbzF1Rz-gsxszWA2xIIpwrc=',
                        'X-WallTech-Date: Mon, 19 Dec 2022 12:17:01 GMT',
                        'Accept: application/json',
                        'Content-Type: application/json'
                    ),
                    ));
                    $labelResponse = curl_exec($curl);
                    curl_close($curl);
                    $labelData = json_decode($labelResponse);
                    
                    if($labelData->status == "Success") {
                        if ($labelData->data[0]->labelContent){
                            $order->update([
                                'corrios_tracking_code' => $trackingNumber,
                                'api_response' => json_encode($labelData),
                                'cn23' => [
                                    "tracking_code" => $trackingNumber,
                                    "stamp_url" => route('warehouse.cn23.download',$order->id),
                                    'leve' => false
                                ],
                            ]);
                            // store order status in order tracking
                            return $this->addOrderTracking($order);
                        }
                    }
                    if($labelData->status == "Failure") {
                        return new PackageError("Error while printing label. Code: ".$data->errors[0]->code.' Description: '.$data->errors[0]->message);
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

    public function registerDeliveryBillGePS(DeliveryBill $deliveryBill)
    {
        $manifest = [
            'manifest' => [
                'manifestnbr' => "HD".'-'.$deliveryBill->containers[0]->destination_operator_name.''.$deliveryBill->containers[0]->id,
            ],
        ];
        try {
            $response = $this->client->post('https://globaleparcel.com/api.aspx',[
                'headers' => $this->getKeys(),
                'json' => $manifest,
                ]);
            $data = json_decode($response->getBody()->getContents());
            if (isset($data->err)) {
                return [
                    'success' => false,
                    'message' => $data->err ?? 'Something Went Wrong! Please Try Again..',
                    'data' => null
                ];
            }

            return [
                'success' => true,
                'data' => $data
            ];
        }catch (\GuzzleHttp\Exception\ClientException $e) {
            return new PackageError($e->getResponse()->getBody()->getContents());
        }
        catch (\Exception $exception){
            return new PackageError($exception->getMessage());
        }
    }

    public function downloadGePSManifest(DeliveryBill $deliveryBill)
    {
        $manifest = [
            'manifest' => [
                'manifestnbr' => "HD".'-'.$deliveryBill->containers[0]->destination_operator_name.''.$deliveryBill->containers[0]->id,
            ],
        ];
        try {
            $response = $this->client->post('https://globaleparcel.com/api.aspx',[
                'headers' => $this->getKeys(),
                'json' => $manifest,
                ]);
            $data = json_decode($response->getBody()->getContents());
            if (isset($data->err)) {
                return [
                    'success' => false,
                    'message' => $data->err ?? 'Something Went Wrong! Please Try Again..',
                    'data' => null
                ];
            }

            return [
                'success' => true,
                'data' => $data
            ];
        }catch (\GuzzleHttp\Exception\ClientException $e) {
            return new PackageError($e->getResponse()->getBody()->getContents());
        }
        catch (\Exception $exception){
            return new PackageError($exception->getMessage());
        }
    }

    public function cancelShipment($trackCode)
    {
        $cancelRequest = [
            'cancelshipment' => [
                'tracknbr' => $trackCode
            ],
        ];
        try {
            $response = $this->client->post('https://globaleparcel.com/api.aspx',[
                'headers' => $this->getKeys(),
                'json' => $cancelRequest,
                ]);
            $data = json_decode($response->getBody()->getContents());
            if (isset($data->err)) {
                return [
                    'success' => false,
                    'message' => $data->err ?? 'Something Went Wrong! Please Try Again..',
                    'data' => null
                ];
            }
            return [
                'success' => true,
                'data' => $data
            ];
        }catch (\GuzzleHttp\Exception\ClientException $e) {
            return new PackageError($e->getResponse()->getBody()->getContents());
        }
        catch (\Exception $exception){
            return new PackageError($exception->getMessage());
        }
    }

}
