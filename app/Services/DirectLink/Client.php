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
    protected $host;
    protected $orderLabelUrl;
    //direct link parameters end
    protected $client;

    public function __construct()
    {
        if(app()->isProduction()){
            $this->host = config('direct_link.production.host');
            $this->orderLabelUrl = config('direct_link.production.orderLabelUrl');
        }else{ 
            $this->host = config('direct_link.test.host');
            $this->orderLabelUrl = config('direct_link.test.orderLabelUrl');
        }

        $this->client = new GuzzleClient();

    }

    private function getHeader()
    {
        return [
            'Host'=> $this->host,
            'X-WallTech-Date' => 'Tue, 20 Dec 2022 19:21:56 GMT',
            'Authorization' => 'WallTech testa0wXdbpML6JGQ7NRP3O:tQ3_LcMlDhGBBxCVynLyMworNw4=',
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ]; 
    }

    public function createPackage($order)
    {   
        $shippingRequest = (new ShippingOrder())->getRequestBody($order);
        try {

            $response = Http::withHeaders($this->getHeader())->post('http://qa.etowertech.com/services/shipper/orderLabels', $shippingRequest);
            $data = json_decode($response);
            if($data->status == "Success") {
                $trackingNumber = $data->data[0]->trackingNo;
                if ($trackingNumber){
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

            $response = Http::withHeaders($this->getHeader())->delete("http://qa.etowertech.com/services/shipper/order/{$orderId}");
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
        }catch (\GuzzleHttp\Exception\ClientException $e) {
            return new PackageError($e->getResponse()->getBody()->getContents());
        }
        catch (\Exception $exception){
            return new PackageError($exception->getMessage());
        }
    }

}
