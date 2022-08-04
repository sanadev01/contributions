<?php

namespace App\Services\GePS;

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
    protected $chargableWeight;

    public function __construct()
    {
        $this->client = new GuzzleClient([

        ]);
    }

    private function getKeys()
    {
        $headers = [

            'Authorization: Basic' => "Z3BwOmFwaXRlc3Q=",
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
                $itemToPush = [
                    'description' => $item->description,
                    'qty' => (int)$item->quantity,
                    //'total_weight_grams' => (int)$singleItemWeight / (int)$item->quantity,
                    'value' => $item->value,
                    'hscode' => "$item->sh_code",
                    'currency' => "USD",
                    'origin' => $order->senderCountry->code,
                    'exportreason' => 'sale',
                    'exporttype' => 'Permanent',
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
            'servicecode' => "KP",
            'reference' => ($order->customer_reference) ? $order->customer_reference : '',
            'custtracknbr' => "123-44557",
            'uom' => "KG",
            'weight' => (int)$weight,
            'length' => $order->length,
            'width' => $order->width,
            'height' => $order->height,
            'inco' => "DDU",
            'contentcategory' => "NP",
            'shipperaddress' => [
                'name' => $order->sender_first_name.' '.$order->sender_last_name,
                'addr1' => $order->sender_address,
                'state' => ($order->sender_state) ? $order->sender_state : '',
                'city' => $order->sender_city,
                'country' => $order->senderCountry->name,
                'postal' => cleanString($order->sender_zipcode),
                'phone' => ($order->sender_phone) ? $order->sender_phone : '',
                'email' => ($order->sender_email) ? $order->sender_email : '',
            ],
            'consigneeaddress' => [
                'name' => $order->recipient->getFullName(),
                'addr1' => $order->recipient->address,
                'state' => $order->recipient->state->code,
                'city' => $order->recipient->city,
                'country' => $order->recipient->country->name,
                'postal' => cleanString($order->recipient->zipcode),
                'phone' => ($order->recipient->phone) ? $order->recipient->phone: '',
                'taxid' => '',
            ],
            'item' => $this->setItemsDetails($order)
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
