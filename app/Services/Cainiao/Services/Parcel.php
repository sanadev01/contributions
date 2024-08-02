<?php

namespace App\Services\Cainiao\Services;

use Carbon\Carbon;
use App\Models\Order;
use App\Services\Converters\UnitsConverter;

class Parcel
{
    protected $chargableWeight;
    protected $order;
    protected $weight;

    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->weight = $order->weight;
        if (!$this->order->isWeightInKg()) {
            $this->weight = UnitsConverter::poundToKg($this->order->getOriginalWeight('lbs'));
        }
    }

    public function getRequestBody()
    {
        $refNo = $this->order->customer_reference;
        $customerReference = ($this->order->customer_reference ? $this->order->customer_reference : $this->order->tracking_id) . ' HD-333' . $this->order->id;

        return [
            "syncGetTrackingNumber" => true,
            "outOrderId" => $customerReference,
            "receiverParam" => [
                "zipCode" => $this->order->recipient->zipcode ?? "25025102",
                "mobilePhone" => $this->order->recipient->phone ?? "+5521966321087",
                "city" => $this->order->recipient->city ?? "Duque de Caxias",
                "countryCode" => $this->order->recipient->country->code ?? "BR",
                "street" => $this->order->recipient->address ?? "Avenida Henrique Valadares 1536",
                "district" => $this->order->recipient->district ?? "",
                "name" => $this->order->recipient->getFullName() ?? "Igor Frank",
                "detailAddress" => $this->order->recipient->address2 ?? "Casa Parque Lafaiete",
                "telephone" => $this->order->recipient->phone ?? "+5521966321087",
                "state" => $this->order->recipient->state->code ?? "RJ",
                "email" => $this->order->recipient->email ?? "yvqbw91k97vpg0f@marketplace.amazon.com.br",
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
                    "length" => $this->order->length ?? 16,
                    "width" => $this->order->width ?? 11,
                    "height" => $this->order->height ?? 2,
                    "weight" => $this->weight
                ]
            ],
            "senderParam" => [
                "zipCode" => $this->order->sender_zipcode ?? "518109",
                "mobilePhone" => $this->order->sender_phone ?? "",
                "city" => $this->order->sender_city ?? "shenzhen",
                "countryCode" => $this->order->sender_country->code ?? "CN",
                "street" => $this->order->sender_address ?? "",
                "district" => $this->order->sender_district ?? null,
                "name" => $this->order->getSenderFullName() ?? "Hrich",
                "detailAddress" => $this->order->sender_address2 ?? "Building 17, Fumao New Village,Sanlian Community",
                "telephone" => $this->order->sender_phone ?? "18063210240",
                "state" => $this->order->sender_state->code ?? "GD",
                "email" => $this->order->sender_email ?? "hrich1@163.com",
                "addressId" => $this->order->sender_address_id ?? null
            ],
            "sourceHandoverParam" => [
                "type" => "PORT",
                "code" => "GRU"
            ]
        ];
    }

    private function mapItemParams()
    {
        return array_map(function($item) {
            return [
                "unitPrice" => $item->unit_price ?? 50,
                "englishName" => $item->english_name ?? "smart watch",
                "itemType" => $item->item_type ?? "cf_normal",
                "clearanceShipUnitPrice" => $item->clearance_ship_unit_price ?? 0,
                "clearanceVat" => $item->clearance_vat ?? null,
                "quantity" => $item->quantity ?? 1,
                "unitPriceCurrency" => $item->unit_price_currency ?? "USD",
                "hscode" => $item->hscode ?? "8517629900",
                "msds" => $item->msds ?? "",
                "weight" => $item->weight ?? "200",
                "clearanceShipVat" => $item->clearance_ship_vat ?? null,
                "clearanceUnitPrice" => $item->clearance_unit_price ?? null,
                "itemId" => $item->item_id ?? "C20-black",
                "taxRate" => $item->tax_rate ?? 0,
                "taxCurrency" => $item->tax_currency ?? "USD",
                "chineseName" => $item->chinese_name ?? "智能手表",
                "itemUrl" => $item->item_url ?? "aliexpress.com"
            ];
        }, $this->order->items->toArray());
    }
}