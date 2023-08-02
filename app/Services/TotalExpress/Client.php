<?php

namespace App\Services\TotalExpress;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderTracking;
use App\Models\ShippingService;
use App\Models\Warehouse\Container;
use Illuminate\Support\Facades\Http;
use App\Models\Warehouse\DeliveryBill;
use App\Services\TotalExpress\Services\Parcel;
use GuzzleHttp\Client as GuzzleClient;
use App\Services\Converters\UnitsConverter;
use App\Services\Correios\Contracts\Package;
use App\Services\Correios\Models\PackageError;

class Client
{

    protected $email;
    protected $password;
    protected $baseUrl;
    protected $token;
    protected $client;

    public function __construct()
    {
        if (app()->isProduction()) {
            $this->email = config('total_express.production.email');
            $this->password = config('total_express.production.password');
            $this->baseUrl = config('total_express.production.baseUrl');
            $this->baseAuthUrl = config('total_express.production.baseAuthUrl');
        } else {
            $this->email = config('total_express.test.email');
            $this->password = config('total_express.test.password');
            $this->baseUrl = config('total_express.test.baseUrl');
            $this->baseAuthUrl = config('total_express.test.baseAuthUrl');
        }




        $this->client = new GuzzleClient();
        $authParams = [
            'email' => $this->email,
            'password' => $this->password
        ];
        $response = $this->client->post($this->baseAuthUrl, ['json' => $authParams]);
        $data = json_decode($response->getBody()->getContents());
        if ($data->auth_token) {
            $this->token = $data->auth_token;
        }
    }

    private function getHeaders()
    {
        return [
            'Authorization' => "Bearer {$this->token}",
            'Accept' => 'application/json'
        ];
    }

    public function createPackage(Order $order)
    {
        $shippingRequest = (new Parcel($order))->getRequestBody();

        try {
            $request = Http::withHeaders($this->getHeaders())->post("$this->baseUrl/v1/orders", $shippingRequest);
            $response = json_decode($request); 
              
            if ($response->status=="SUCCESS" && $response->data && $response->data->id) {
                $id = $response->data->id; 
                $getLabel = Http::withHeaders($this->getHeaders())->put("$this->baseUrl/v1/orders/$id/cn23-merged");
                 
                $getLabelResponse = json_decode($getLabel);
                if($getLabelResponse->status=="SUCCESS") {
                    $mergedResponse = [
                        'orderResponse' => $request,
                        'labelResponse' => $getLabel,
                    ];
                    $order->update([
                        'corrios_tracking_code' => $response->reference,
                        'api_response' => json_encode($mergedResponse),
                        'cn23' => [
                            "tracking_code" => $response->reference,
                            "stamp_url" => route('warehouse.cn23.download',$order->id),
                            'leve' => false
                        ],
                    ]);
                    // store order status in order tracking
                    return $this->addOrderTracking($order);
                }
                if($getLabelResponse->status=="ERROR") {
                    
                    return new PackageError(new HandleError($getLabel));
                }
            } else {
                return new PackageError(new HandleError($request));
            }
        } catch (\Exception $exception) {
            return new PackageError($exception->getMessage());
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

}
