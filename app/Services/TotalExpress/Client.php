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
use App\Services\TotalExpress\Services\Overpack;

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
        } else {
            $this->email = config('total_express.test.email');
            $this->password = config('total_express.test.password');
            $this->baseUrl = config('total_express.test.baseUrl'); 
        }




        $this->client = new GuzzleClient();
        $authParams = [
            'email' => $this->email,
            'password' => $this->password
        ];
        $response = $this->client->post("$this->baseUrl/authenticate/total/seller", ['json' => $authParams]);
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
        $apiResponse = json_decode($order->api_response); 
        try {
                if(!$order->api_response){
                    $request = Http::withHeaders($this->getHeaders())->post("$this->baseUrl/v1/orders", $shippingRequest);
                    $response = json_decode($request); 

                    if ($response->status=="SUCCESS" && $response->data && $response->data->id ) {
                
                        $mergedResponse = [
                            'orderResponse' => $response,
                            'labelResponse' => null,
                        ];
                        $order->update([
                                    'api_response' => json_encode($mergedResponse),
                        ]);
                        $order->refresh();

                    } else {
                        return new PackageError(new HandleError($request));
                    }
                }
                if($order->api_response) {
                    $apiResponse = json_decode($order->api_response); 
                    $response = $apiResponse->orderResponse; 
                    $id = $response->data->id;  
                    $getLabel = Http::withHeaders($this->getHeaders())->put("$this->baseUrl/v1/orders/$id/cn23-merged");
                    $getLabelResponse = json_decode($getLabel);

                    if ($getLabelResponse->status=="SUCCESS"){
                        $mergedResponse = [
                        'orderResponse' => $response,
                        'labelResponse' => $getLabelResponse,
                        ];

                        $order->update([
                            'corrios_tracking_code' => optional(optional($getLabelResponse->data)->cn23_numbers)[0],
                            'api_response' => json_encode($mergedResponse),
                            'cn23' => [
                                "tracking_code" =>  optional(optional($getLabelResponse->data)->cn23_numbers)[0],
                                "stamp_url" => route('warehouse.cn23.download',$order->id),
                                'leve' => false
                            ],
                        ]);
                    }
                    else{
                        return new PackageError(new HandleError($getLabel));
                    }

                    // store order status in order tracking
                    return $this->addOrderTracking($order);
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

    function registerUnit($container) {
        
        try{
                $overpack = new Overpack($container);
                $overpackRequest =  $overpack->getRequestBody();
                $request = Http::withHeaders($this->getHeaders())->post("$this->baseUrl/v1/overpacks", $overpackRequest);
                $response = json_decode($request);
                \Log::info('over pack');
                \Log::info([$response]);
                if($response->status=="SUCCESS"){
                    $container->update([
                    'unit_code' => $response->data->reference,
                    'unit_response_list' => $request,
                    'response' => '1',
                ]);
                return [
                    'type'=>'alert-success',
                    'message'=>'Package Registration success. You can print Label now'
                ];

            }
            else{ 
                return [ 
                    'type'=>'alert-danger',
                    'message'=> ''.new HandleError($request)
                ]; 
            }

           
        }catch(\Throwable $e){
            
            return [
                'type'=>'alert-danger',
                'message'=>$e->getMessage()
            ];
        }
            
    }
    function overpackLabel($container) {
          
        try{
            $response = json_decode($container->unit_response_list);
            $data =  $response->data;
            $request = Http::withHeaders($this->getHeaders())->put("$this->baseUrl/v1/overpacks/$data->id/label");
            $response = json_decode($request);
            \Log::info('over pack label');
            \Log::info([$response]);
            if($response->status=="SUCCESS"){
            return [
                'type'=>'alert-success',
                'message'=>'label'
            ];

        }
        else{ 
            return [ 
                'type'=>'alert-danger',
                'message'=> ''.new HandleError($request)
            ]; 
        }

       
    }catch(\Throwable $e){
        
        return [
            'type'=>'alert-danger',
            'message'=>$e->getMessage()
        ];
    }
    }

}
