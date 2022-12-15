<?php

namespace App\Services\DirectLink;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderTracking;
use App\Models\ShippingService;
use Illuminate\Support\Facades\Cache;
use App\Models\Warehouse\DeliveryBill;
use GuzzleHttp\Client as GuzzleClient;
use App\Services\Converters\UnitsConverter;
use App\Services\Calculators\WeightCalculator;
use App\Services\Correios\Models\PackageError;
use App\Services\DirectLink\Services\ShippingOrder;
use Illuminate\Support\Facades\Log;

class Client{

    //direct link parameters
    protected $base_url; 
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
            $this->token = config('direct_link.production.token');
            $this->host = config('direct_link.production.host');
            $this->date_time = config('direct_link.production.date_time');
        }else{ 
            
            $this->base_url = config('direct_link.test.base_url');
            $this->token = config('direct_link.test.token');
            $this->host = config('direct_link.test.host');
            $this->date_time = config('direct_link.test.date_time');
        }

        $this->client = new GuzzleClient(['base_uri' => $this->base_url]);

    }


    // private function calulateItemWeight($order)
    // {
    //     $orderTotalWeight = ($this->chargableWeight != null) ? (float)$this->chargableWeight : (float)$order->weight;
    //     $itemWeight = 0;

    //     if (count($order->items) > 1) {
    //         $itemWeight = $orderTotalWeight / count($order->items);
    //         return $itemWeight;
    //     }
    //     return $orderTotalWeight;
    // }

    // private function setItemsDetails($order)
    // {
    //     $items = [];
    //     $singleItemWeight = UnitsConverter::kgToGrams($this->calulateItemWeight($order));
        
    //     if (count($order->items) >= 1) {
    //         foreach ($order->items as $key => $item) {
    //             $itemToPush = [];
    //             $originCountryCode = optional($order->senderCountry)->code;
    //             $itemToPush = [
    //                 'description' => $item->description,
    //                 'qty' => (int)$item->quantity,
    //                 'value' => number_format($item->value * (int)$item->quantity , 2),
    //                 'hscode' => "$item->sh_code",
    //                 'currency' => "USD",
    //                 'origin' => $originCountryCode ? $originCountryCode: 'US',
    //                 'exportreason' => 'Sale of Goods',
    //                 'exporttype' => 'Permanent',
    //             ];
    //            array_push($items, $itemToPush);
    //         }
    //     }

    //     return $items;
    // }

    // public function calculateVolumetricWeight($order)
    // {
    //     if ( $order->measurement_unit == 'kg/cm' ){

    //         $volumetricWeight = WeightCalculator::getVolumnWeight($order->length,$order->width,$order->height,'cm');
    //         return $this->chargableWeight = round($volumetricWeight >  $order->weight ? $volumetricWeight :  $order->weight);

    //     }else{

    //         $volumetricWeight = WeightCalculator::getVolumnWeight($order->length,$order->width,$order->height,'in');
    //        return $this->chargableWeight = round($volumetricWeight >  $order->weight ? $volumetricWeight :  $order->weight);
    //     }
    // }

    // private function calculateItemsValue($orderItems)
    // {
    //     $itemsValue = 0;
    //     foreach ($orderItems as $item) {
    //         $itemsValue += $item->value * $item->quantity;
    //     }

    //     return $itemsValue;
    // }

    public function createPackage($order)
    {   
        
    
         $shippingOrderRequest =(new ShippingOrder())->getRequestBody();  
         Log::info('response direct link');
         Log::info($this->getHeader());
         Log::info($shippingOrderRequest
        );
          
            $response = $this->client->post('/services/shipper/orders',[
                'headers' => $this->getHeader(),
                'json' => json_encode($shippingOrderRequest),
            ]);
            Log::info('response direct link 1');

         return dd($shippingOrderRequest);
            Log::info($response);

            return dd($response);
            $data = json_decode($response->getBody()->getContents());
            if(isset($data->err)) {
                return new PackageError($data->err);
            }
            dd('hello');        
               return $trackingNumber = $data->shipmentresponse->tracknbr;

            if ( $trackingNumber ){
                $order->update([
                    'corrios_tracking_code' => $trackingNumber,
                    'api_response' => json_encode($data),
                    'cn23' => [
                        "tracking_code" => $trackingNumber,
                        "stamp_url" => route('warehouse.cn23.download',$order->id),
                        'leve' => false
                    ],
                ]);
                // add orders to container
                // $container->orders()->attach($order->id);
                // store order status in order tracking
                return $this->addOrderTracking($order);
            }
            return null;
        // }catch (\GuzzleHttp\Exception\ClientException $e) {
        //     return new PackageError($e->getResponse()->getBody()->getContents());
        // }
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

    
    private function getHeader()
    {
        return [
            'X-WallTech-Date '=> $this->date_time,
            'Authorization:' => $this->token,
            'Host:' => $this->host,
            'Accept' => "application/json",
            'Content-Type' => "application/json",

        ]; 
    }

}
