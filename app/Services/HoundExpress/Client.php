<?php

namespace App\Services\HoundExpress;

use App\Models\Order;
use App\Models\OrderTracking;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client as GuzzleClient;
use App\Services\Correios\Models\PackageError;
use App\Services\HoundExpress\Services\CN23\HoundErrorHandler;
use App\Services\HoundExpress\Services\CN23\HoundOrder;
class Client{

    //Sweden Post Parameters 
    private $baseUrl;
    private $partnerKey;

    public function __construct()
    {
        if (app()->isProduction()) {
            $this->partnerKey = config('hound.production.partner_key');
            $this->baseUrl = config('hound.production.base_url');
        } else {
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
    public function generateLabel($order)
    {
        $order_response = json_decode($order->api_response);

        $response = Http::withHeaders($this->getHeaders())->post($this->baseUrl . '/Sabueso/ws/deliveryServices/getLabel', [                
                "guideNumber"=> $order_response->guideNumber,
                "isReturn"=> false
        ]);
        $response_body = json_decode($response->getBody()); 
        $byteArray = $response_body->format; 
        // Specify the file path where you want to save the PDF
        $filePath =   storage_path("app/labels/{$order->corrios_tracking_code}.pdf");
        // Convert the byte array to binary data
        $binaryData = pack('C*', ...$byteArray); 
        // Write the binary data to the PDF file
        file_put_contents($filePath, $binaryData); 
    }
    public function createPackage($order)
    {
        $houndOrderRequest = (new HoundOrder($order))->getRequestBody();
        try {
            if(!$order->api_response){
                $response = Http::withHeaders($this->getHeaders())->post($this->baseUrl . '/Sabueso/ws/deliveryServices/createOrder', $houndOrderRequest);
                $response_body = json_decode($response->getBody());
                $error = (new HoundErrorHandler($response_body))->getError();
                if ($error) {
                    return new PackageError($error);
                } else {
                    $response = json_encode($response_body);
                    $order->update([
                        'api_response'=> $response,
                        'corrios_tracking_code'=> $response_body->guideNumber,
                    ]);
                }
            }

            $this->generateLabel($order);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return new PackageError($e->getResponse()->getBody()->getContents());
        }
    }

    public function addOrderTracking($order)
    {
        if ($order->trackings->isEmpty()) {
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
            $response = Http::withHeaders($this->getHeaders("DELETE", $path))->delete($this->baseUrl . $path);
            $data = json_decode($response);
            if ($data->status == "Failure") {
                return [
                    'success' => false,
                    'message' => "Error while shipment cancellation. Code: " . $data->errors[0]->code . ' Description: ' . $data->errors[0]->message,
                    'data' => null
                ];
            }
            return [
                'success' => true,
                'data' => $data
            ];
        } catch (\Exception $exception) {
            return new PackageError($exception->getMessage());
        }
    }
}
