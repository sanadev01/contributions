<?php

namespace App\Services\PostNL;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderTracking;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Models\Warehouse\DeliveryBill;
use App\Services\Correios\Models\PackageError;
use App\Services\Calculators\WeightCalculator;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client as GuzzleClient;
use App\Services\Converters\UnitsConverter;

class Client{

    protected $client;
    protected $createLabelUrl;
    protected $cancelLabelUrl;
    protected $createAssistLabelUrl;
    protected $createManifest;
    protected $chargableWeight;

    public function __construct($createLabelUrl=null, $cancelLabelUrl=null, $createAssistLabelUrl=null, $createManifest=null)
    {
        $this->client = new GuzzleClient([

        ]);

        if (app()->isProduction()) {
            // POSTNL Api Production Environment Credentials
            $createLabelUrl = config('postnl.production.createLabelUrl');
            $cancelLabelUrl = config('postnl.production.cancelLabelUrl');
            $createAssistLabelUrl = config('postnl.production.createAssistLabelUrl');
            $createManifest = config('postnl.production.createManifest');
        }else {

            // POSTNL Api Testing Environment Credentials
            $createLabelUrl = config('postnl.testing.createLabelUrl');
            $cancelLabelUrl = config('postnl.testing.cancelLabelUrl');
            $createAssistLabelUrl = config('postnl.testing.createAssistLabelUrl');
            $createManifest = config('postnl.testing.createManifest');
        }
        $this->createLabelUrl = $createLabelUrl;
        $this->cancelLabelUrl = $cancelLabelUrl;
        $this->createAssistLabelUrl = $createAssistLabelUrl;
        $this->createManifest = $createManifest;
    }

    private function getKeys()
    {
        $headers = [

            'api_key' => "Eo3qtkGlOh6t9S1HZxMvFkBSJYDTocatwMhBNwhnEoG7Jngng89GtVFmQOrc05OzcMwyLMTeQSYU2h4GsOOp0iy9Rp0qoYlhpGLfLpjNc8CuV3xqbrTGFYNkiZW6TWzdJWVgEsVLg64hYMLY1UElGjrOvxBpA4aI5prbWIefoMrd85y5WkuL1RQrfkH9vRCwod0v8feftgdEeZLYUkQWfYa1TVeeEe4fcbdk9twD6ynpjmq4E7FSLwdeiFIhqicw7a1kY63Bksp5ECq1pefkn0ROrCNjpy3TPdeLKO5I6LBc",
            'Accept' => "application/json",
            'Content-Type' => "application/json",

        ];
        return $headers;
    }

    private function calulateItemWeight($order)
    {
        $orderTotalWeight = ($this->chargableWeight != null) ? (float)$this->chargableWeight : (float)$order->weight;
        $itemWeight = 0;

        if (count($order->items) > 1) {
            $itemWeight = $orderTotalWeight / count($order->items);
            return $itemWeight;
        }
        return $orderTotalWeight;
    }

    private function setItemsDetails($order)
    {
        $items = [];
        $singleItemWeight = UnitsConverter::kgToGrams($this->calulateItemWeight($order));

        if (count($order->items) >= 1) {
            foreach ($order->items as $key => $item) {
                $itemToPush = [];
                $originCountryCode = optional($order->senderCountry)->code;
                $itemToPush = [
                    'goods_description' => $item->description,
                    'quantity' => (int)$item->quantity,
                    'total_weight_grams' => (int)$singleItemWeight / (int)$item->quantity,
                    'total_goods_value' => number_format($item->value * (int)$item->quantity , 2),
                    'total_goods_value_eur' => '',
                    'tariff' => "$item->sh_code",
                    'manufacture_country_code' => $originCountryCode ? $originCountryCode: 'US',
                ];
               array_push($items, $itemToPush);
            }
        }

        return $items;
    }

    public function calculateVolumetricWeight($order)
    {
        if ( $order->measurement_unit == 'kg/cm' ){

            $volumetricWeight = WeightCalculator::getVolumnWeight($order->length,$order->width,$order->height,'cm');
            return $this->chargableWeight = round($volumetricWeight >  $order->weight ? $volumetricWeight :  $order->weight);

        }else{

            $volumetricWeight = WeightCalculator::getVolumnWeight($order->length,$order->width,$order->height,'in');
           return $this->chargableWeight = round($volumetricWeight >  $order->weight ? $volumetricWeight :  $order->weight);
        }
    }

    private function calculateItemsValue($orderItems)
    {
        $itemsValue = 0;
        foreach ($orderItems as $item) {
            $itemsValue += $item->value * $item->quantity;
        }

        return $itemsValue;
    }

    public function createPackage($order)
    {
        if($order->isWeightInKg()) {
            $weight = UnitsConverter::kgToGrams($order->getWeight('kg'));
        }else{
            $kg = UnitsConverter::poundToKg($order->getWeight('lbs'));
            $weight = UnitsConverter::kgToGrams($kg);
        }
        $packet =
        [
            'product_code' => "MBX",
            'label_type' => "PDF",
            'customer_reference_number' => ($order->customer_reference) ? $order->customer_reference : '',
            'gross_weight_grams' => (int)$weight,
            'declaration_type' => 'SaleOfGoods',
            'dangerous_goods' => false,
            'currency' => "USD",
            'return' => true,
            //'insurance_value' => '0',
            'sender_details' => [
                'name' => $order->sender_first_name.' '.$order->sender_last_name,
                'company' => $order->user->pobox_number,
                'address' => ($order->sender_address)?  $order->sender_address : '2200 NW 129TH AVE',
                'postal_code' => ($order->sender_zipcode)? $order->sender_zipcode : '33182',
                'city' => ($order->sender_city)? $order->sender_city : 'Miami',
                'state' => ($order->sender_state)? $order->sender_state : 'FL',
                'country' => ($order->senderCountry->name)? $order->senderCountry->name : 'United States',
                'country_code' => ($order->senderCountry->code)? $order->senderCountry->code : 'US',
                'email' => ($order->sender_email) ? $order->sender_email : '',
                'phone' => ($order->sender_phone) ? $order->sender_phone : '',
            ],
            'addressee_details' => [
                'name' => $order->recipient->getFullName(),
                'address' => $order->recipient->address,
                'postal_code' => cleanString($order->recipient->zipcode),
                'city' => $order->recipient->city,
                'state' => $order->recipient->state->code,
                'country' => $order->recipient->country->name,
                'country_code' => $order->recipient->country->code,
                'phone' => $order->recipient->phone,
            ],
            'content_pieces' => $this->setItemsDetails($order)
        ];
        \Log::info(
            $packet
        );
        try {
            $response = $this->client->post($this->createLabelUrl,[
                'headers' => $this->getKeys(),
                'json' => $packet
            ]);

            $data = json_decode($response->getBody()->getContents());
            if($data->status !== "success" ) {
                return new PackageError($data->message->payload);
            }
            $trackingNumber = $data->data->item;

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

                // store order status in order tracking
                return $this->addOrderTracking($order);
            }
            return null;
        }catch (\GuzzleHttp\Exception\ClientException $e) {
            return new PackageError($e->getResponse()->getBody()->getContents());
        }
    }

    public function createContainer($container)
    {
        try {
            $response = $this->client->post($this->createAssistLabelUrl,[
                'headers' => $this->getKeys(),
                'json' => [
                    "label_type" => "PDF",
                    "destination_country_code" => $container->destination_operator_name,
                ]
            ]);
            $data = json_decode($response->getBody()->getContents());
            if ($data->status == 'fail') {
                return [
                    'success' => false,
                    'message' => $data->message->payload ?? 'Something Went Wrong! Please Try Again..',
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

    public function registerDeliveryBillPostNL(DeliveryBill $deliveryBill)
    {
        $items = [];
        foreach ($deliveryBill->containers[0]->orders as $key => $item) {
            $itemToPush = [
                $item->corrios_tracking_code,
            ];
            array_push($items, $itemToPush[0]);
        }
        $barcode = '';
        $barcode = $deliveryBill->containers[0]->unit_code;
        try {
            $response = $this->client->post($this->createManifest,[
                'headers' => $this->getKeys(),
                'json' => [
                    "type" => "ASSISTLABEL",
                        "assistlabel_item" => [
                            'assistlabel' => $barcode,
                            'receptacle_type' => "BG",
                            'format' => "E",
                            'product_code' => "MBX",
                            'items' => $items,
                        ],
                    ],
                ]);
            $data = json_decode($response->getBody()->getContents());
            if ($data->status == 'fail') {
                return [
                    'success' => false,
                    'message' => $data->message->payload ?? 'Something Went Wrong! Please Try Again..',
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
        try {
            $response = $this->client->delete("/packet/v1/units/dispatch/$container->dispatch_number",[
                'headers' => [
                    'Authorization' => "Bearer {$this->getToken()}"
                ]
            ]);
            return $response;
        }catch (\GuzzleHttp\Exception\ClientException $e) {
            return new PackageError($e->getResponse()->getBody()->getContents());
        }
        catch (\Exception $exception){
            return new PackageError($exception->getMessage());
        }
    }

}
