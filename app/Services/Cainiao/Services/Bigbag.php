<?php

namespace App\Services\Cainiao\Services;
use App\Models\Warehouse\Container; 

class Bigbag
{ 
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getRequestBody()
    { 
        $weight = $this->container->getWeight();
        return [
            "request" => [
                "locale" => "zh_cn",
                "weight" => "$weight",
                "weightUnit" => "kg",
                "orderCodeList" => $this->mapItemParams(),
                "handoverParam" => [
                    "zipCode" => "310000",
                    "mobilePhone" => "18666270000",
                    "city" => "杭州市",
                    "addressId" => "",
                    "telephone" => "",
                    "street" => "Chiang Village Street",
                    "district" => "",
                    "name" => "Hrich",
                    "detailAddress" => "西湖区蒋村街道龙湖天街",
                    "country" => "CN",
                    "countryCode" => "CN",
                    "portCode" => "GRU",
                    "state" => "浙江省",
                    "email" => ""
                ]
            ]
        ];
    }
    private function mapItemParams()
    {
        $items = [];
        foreach ($this->container->orders as $order) {
            $api_response = json_decode($order->api_response);
            $items[] = $api_response->data->orderCode;
        }
        return $items;
    }
}
