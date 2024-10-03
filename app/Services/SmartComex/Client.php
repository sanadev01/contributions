<?php

namespace App\Services\SmartComex;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderTracking;
use App\Models\ShippingService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\Warehouse\DeliveryBill;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Storage;
use App\Services\Converters\UnitsConverter;
use App\Services\SmartComex\Services\Parcel;
use App\Services\Calculators\WeightCalculator;
use App\Services\Correios\Models\PackageError;

class Client{

    protected $client;
    protected $apiKey;
    protected $apiSecret;


    public function __construct(Order $order)
    {
        $services = [
            'fox_courier' => 'fox_courier',
            'phx_courier' => 'phx_courier'
        ];
        
        $environment = app()->isProduction() ? 'production' : 'test';
        
        foreach ($services as $service => $configKey) {
            if ($order->shippingService->{'is_' . $service}) {
                $this->apiKey = config("{$configKey}.{$environment}.api_key");
                $this->apiSecret = config("{$configKey}.{$environment}.api_secret");
                break;
            }
        }     

        $this->client = new GuzzleClient();

    }

    private function getHeaders()
    {
        $authString = $this->apiKey . ':' . $this->apiSecret;
        $encodedAuth = base64_encode($authString);
        return [
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . $encodedAuth,
        ];
    }

    public function createPackage($order)
    {
        $parcel = new Parcel($order);
        $shippingRequest = $parcel->getRequestBody();
        
        try {
            $response = $this->client->post('https://api.smartcomex.io/api-courier/add-shippment', [
                'headers' => $this->getHeaders(),
                'json' => $shippingRequest,
            ]);
            \Log::info(["Fox Api Response" =>$response]);

            $data = json_decode($response->getBody()->getContents(), true);
            \Log::info(["Fox json decode Data " =>$data]);

            if (isset($data[0]['success']) && $data[0]['success']) {
                $trackingNumber = $data[0]['reference'];

                if ($trackingNumber) {
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

                    try {
                        $printLabel = $this->client->get('https://api.smartcomex.io/api-courier/print' . "/" . $trackingNumber, [
                            'headers' => $this->getHeaders()
                        ]);
                        \Log::info(["Fox print api call " =>$printLabel]);
                        $printResponse = $printLabel->getBody()->getContents();
                        \Log::info(["Fox print api response " =>$printResponse]);

                        Storage::put("labels/{$order->corrios_tracking_code}.pdf", $printResponse);
                    } catch (\GuzzleHttp\Exception\ServerException $printException) {
                        $printErrorResponse = json_decode($printException->getResponse()->getBody()->getContents(), true);
                        $printErrorMessage = isset($printErrorResponse['message']) 
                            ? (is_array($printErrorResponse['message']) ? implode(', ', $printErrorResponse['message']) : $printErrorResponse['message']) 
                            : 'Unknown error';
                        return new PackageError("Label Print Error: " . $printErrorMessage);
                    } catch (\Exception $printException) {
                        return new PackageError($printException->getMessage());
                    }
                }
            } else {
                $responseError = isset($data[0]['errors']) ? implode(', ', $data[0]['errors']) : 'Unknown error';
                return new PackageError($responseError);
            }

            return null;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return new PackageError($e->getResponse()->getBody()->getContents());
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            $errorResponse = json_decode($e->getResponse()->getBody()->getContents(), true);
            $errorMessage = isset($errorResponse['message']) 
                ? (is_array($errorResponse['message']) ? implode(', ', $errorResponse['message']) : $errorResponse['message']) 
                : 'Unknown error';
            return new PackageError($errorMessage);
        } catch (\Exception $exception) {
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
