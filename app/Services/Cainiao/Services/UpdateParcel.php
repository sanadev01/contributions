<?php

namespace App\Services\Cainiao\Services;

use Carbon\Carbon;
use App\Models\Order;
use App\Services\Converters\UnitsConverter;

class UpdateParcel
{
    protected $chargableWeight;
    protected $order;
    protected $orignalWeight = 0;

    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->orignalWeight = 100*$this->order->getOriginalWeight();
    }

    public function getRequestBody()
    {
        $api_response = json_decode($this->order->api_response); 
        return ([
            "packageMeasureParam" => [ 
                "length" => $this->order->length,
                "width" => $this->order->width,
                "weight" => 1000 * $this->order->getWeight(),
                "height" =>  $this->order->height

            ],
            "orderCode" =>  $api_response->data->orderCode,
            "sourceHandoverParam" => [
                "code" => "GRU",
                "type" => "PORT"
            ],
            "customerFromPortCode" => "HKG",
            "locale" => "zh_CN",
            "pickupParam" => [
                "zipCode" => $this->order->sender_zipcode,
                "mobilePhone" => $this->order->sender_phone,
                "city" => $this->order->sender_city,
                "countryCode" => $this->order->senderCountry->code,
                "street" => $this->order->sender_address,
                "district" => $this->order->sender_district,
                "name" => $this->order->getSenderFullName(),
                "detailAddress" => $this->order->sender_address,
                "telephone" => $this->order->sender_phone,
                "state" => "GD" ?? $this->order->senderState->code,
                "email" => $this->order->sender_email,
                "addressId" => null
            ],
            "option" => [
                "updatePickupInfo" => "false",
                "updateSourceHandoverInfo" => "true",
                "updatePackageMeasure" => "true"
            ]
        ]);
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
                "weight" => $this->orignalWeight*1000,
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
