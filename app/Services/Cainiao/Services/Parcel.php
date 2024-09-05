<?php

namespace App\Services\Cainiao\Services;

use Carbon\Carbon;
use App\Models\Order;
use App\Services\Converters\UnitsConverter;

class Parcel
{
    protected $chargableWeight;
    protected $order; 
    protected $itemWeight = 0;
    protected $orignalWeight = 0;

    public function __construct(Order $order)
    {
        $this->order = $order;
       
        $this->itemWeight = $this->order->getOriginalWeight() / ($this->order->items->count());
        $this->orignalWeight = $this->order->getOriginalWeight();
    }

    public function getRequestBody()
    {
        $refNo = $this->order->customer_reference;
        $customerReference = ($this->order->customer_reference ? $this->order->customer_reference : $this->order->tracking_id) . ' HD-333' . $this->order->id;
        return [
            "syncGetTrackingNumber" => true,
            "outOrderId" => $customerReference,
            "receiverParam" => [
                "zipCode" => $this->order->recipient->zipcode,
                "mobilePhone" => $this->order->recipient->phone,
                "city" => $this->order->recipient->city,
                "countryCode" => $this->order->recipient->country->code,
                "street" => $this->order->recipient->address,
                "district" => $this->order->recipient->district,
                "name" => $this->order->recipient->getFullName(),
                "detailAddress" => $this->order->recipient->address2,
                "telephone" => $this->order->recipient->phone,
                "state" => $this->order->recipient->state->code,
                "email" => $this->order->recipient->email,
                "addressId" => ""
            ],
            "locale" => "zh_CN",
            "solutionParam" => [
                "importCustomsParam" => [
                    "taxNumber" => "08738558840111"
                ],
                "cainiaoCustomsParam" => [
                    "whetherNeed" => false
                ],
                "solutionCode" => "GM_OPEN_STD_CD"
            ],
            "packageParams" => [
                [
                    "itemParams" => $this->mapItemParams(),
                    "length" => $this->order->length,
                    "width" => $this->order->width,
                    "height" => $this->order->height,
                    "weight" => "$this->orignalWeight"
                ]
            ],
            "senderParam" => [
                "zipCode" => $this->order->sender_zipcode,
                "mobilePhone" => $this->order->sender_phone,
                "city" => $this->order->sender_city,
                "countryCode" => $this->order->senderCountry->code,
                "street" => $this->order->sender_address,
                "district" => $this->order->sender_district,
                "name" => $this->order->getSenderFullName(),
                "detailAddress" => $this->order->sender_address,
                "telephone" => $this->order->sender_phone,
                "state" => $this->order->senderState->code,
                "email" => $this->order->sender_email,
                "addressId" => null
            ],
            "sourceHandoverParam" => [
                "type" => "PORT",
                "code" => "GRU"
            ]
        ];
    }

    private function mapItemParams()
    {
        $items = [];
        foreach ($this->order->items as $item) {
            $items[] = [
                "unitPrice" => $item->value ?? null,
                "englishName" => $item->description,
                "itemType" => 'cf_normal',
                "clearanceShipUnitPrice" => 0,
                "clearanceVat" => null,
                "quantity" => $item->quantity,
                "unitPriceCurrency" => "USD",
                "hscode" => $item->sh_code,
                "msds" => '',
                "weight" => "$this->itemWeight",
                "clearanceShipVat" => null,
                "clearanceUnitPrice" => null,
                "itemId" => 'C20-black',
                "taxRate" => 0,
                "taxCurrency" => "USD",
                "chineseName" => "智能手表",
                "itemUrl" =>  "https://app.homedeliverybr.com"
            ];
        }
        return $items;
    }
}
