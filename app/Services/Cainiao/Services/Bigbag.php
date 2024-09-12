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
        return ([
            "request" => [
                "locale" => "zh_cn",
                "weight" => $this->container->getWeight(),
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
        ]);
    }
    // 组大包的小包LP00676355941098必须先更新实际重量，请调用小包重量更新接口
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
