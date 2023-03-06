<?php

namespace App\Services\GePS;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderTracking;
use App\Models\ShippingService;
use App\Models\Warehouse\Container;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\Warehouse\DeliveryBill;
use GuzzleHttp\Client as GuzzleClient;
use App\Services\Converters\UnitsConverter;
use App\Services\Calculators\WeightCalculator;
use App\Services\Correios\Models\PackageError;

class Client{

    protected $client;
    protected $chargableWeight;

    public function __construct()
    {
        $this->client = new GuzzleClient(['verify' => false]);
    }

    private function getKeys()
    {
        $token = 'Z3BwOmFwaXRlc3Q=';
        if(app()->isProduction()){
            $token = 'aGVyY29hcGk6aGVyY29AMDk4';
        }
        $headers = [
            'Authorization: Basic' => $token,
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
                    'description' => $item->description,
                    'qty' => (int)$item->quantity,
                    'value' => number_format($item->value * (int)$item->quantity , 2),
                    'hscode' => "$item->sh_code",
                    'currency' => "USD",
                    'origin' => $originCountryCode ? $originCountryCode: 'US',
                    'exportreason' => 'Sale of Goods',
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
        //GET CONTAINER FOR PARCEL

        // $container = Container::where('services_subclass_code', ShippingService::GePS)
        //     ->where('destination_operator_name', $order->recipient->country->code)->whereNull('unit_code')->first();

        // if(!$container) {
        //     $container =  Container::create([
        //         'user_id' => Auth::id(),
        //         'seal_no' => '',
        //         'dispatch_number' => 0,
        //         'origin_country' => 'US',
        //         'origin_operator_name' => 'HERC',
        //         'postal_category_code' => 'A',
        //         'destination_operator_name' => $order->recipient->country->code,
        //         'unit_type' => 1,
        //         'services_subclass_code' => ShippingService::GePS
        //     ]);

        //     $container->update([
        //         'dispatch_number' => $container->id
        //     ]);

        //     return $container;

        // }

        
        if($order->isWeightInKg()) {
            $weight = UnitsConverter::kgToGrams($order->getWeight('kg'));
        }else{
            $kg = UnitsConverter::poundToKg($order->getWeight('lbs'));
            $weight = UnitsConverter::kgToGrams($kg);
        }
        if($order->measurement_unit == "lbs/in") {
            $uom = "LB";
        }else {
            $uom = "KG";
        }
        if($order->shippingService->service_sub_class == ShippingService::GePS) {
            $serviceCode = "KP";
        }elseif($order->shippingService->service_sub_class == ShippingService::GePS_EFormat) {
            $serviceCode = "IM";
        }
        elseif($order->shippingService->service_sub_class == ShippingService::Parcel_Post) {
            $serviceCode = "PR";
        }
        $packet =
        [
            'shipment' => [
                    'servicecode' => $serviceCode,
                    'reference' => ($order->customer_reference) ? $order->customer_reference : '',
                    'custtracknbr' => $order->tracking_id,
                    'uom' => $uom,
                    'weight' => $order->weight,
                    'length' => $order->length,
                    'width' => $order->width,
                    'height' => $order->height,
                    'inco' => "DDU",
                    // 'manifestnbr' => "HD".'-'.$container->destination_operator_name.''.$container->dispatch_number,
                    'contentcategory' => "NP",
                'shipperaddress' => [
                    'name' => $order->getSenderFullName(),
                    'addr1' => "2200 NW 129TH AVE",
                    'state' => "FL",
                    'city' => "Miami",
                    'country' => "US",
                    'postal' => "33182",
                    'phone' => ($order->sender_phone) ? $order->sender_phone : '',
                    'email' => ($order->sender_email) ? $order->sender_email : '',
                ],
                'consigneeaddress' => [
                    'name' => $order->recipient->getFullName().' '.$order->warehouse_number,
                    'attention' => $order->customer_reference,
                    'addr1' => $order->recipient->address.' '.$order->recipient->street_no,
                    'addr2' => optional($order->recipient)->address2,
                    'state' => $order->recipient->state->code,
                    'city' => $order->recipient->city,
                    'country' => $order->recipient->country->code,
                    'postal' => cleanString($order->recipient->zipcode),
                    'phone' => ($order->recipient->phone) ? $order->recipient->phone: '',
                    'taxid' => ($order->recipient->tax_id) ? $order->recipient->tax_id: '',
                ],
                'item' => $this->setItemsDetails($order),
                'shippingoptions' => [
                    'hazmat' => 1,
                    'handlinginstructions' => "Land in the backyard, by the pool!"
                ],
            ],
        ];
        \Log::info(
            $packet
        );
        Cache::flush();
        try {
            usleep( 250000 ); //Sleep for quarter of a second. Value is in microseconds
            $response = $this->client->post('https://globaleparcel.com/api.aspx',[
                'headers' => $this->getKeys(),
                'json' => $packet
            ]);

            $data = json_decode($response->getBody()->getContents());
            \Log::info([$data]);
            if(isset($data->err)) {
                return new PackageError($data->err);
            }
            $trackingNumber = $data->shipmentresponse->tracknbr;

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

    public function confirmShipment($trackCode)
    {
        $confirmRequest = [
            'confirmshipment' => [
                'tracknbr' => $trackCode
            ],
        ];
        try {
            $response = $this->client->post('https://globaleparcel.com/api.aspx',[
                'headers' => $this->getKeys(),
                'json' => $confirmRequest,
                ]);
            $data = json_decode($response->getBody()->getContents());
            if (isset($data->err)) {
                return [
                    'success' => false,
                    'message' => $data->error->context ?? 'Something Went Wrong! Please Try Again..',
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
