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
        return ([
            "request" => [
                "locale" => "zh_cn",
                "weight" => "$weight",
                "weightUnit" => "kg",
                "orderCodeList" => $this->mapItemParams(),
                "handoverParam" => [
                    "zipCode" => "01045001",
                    "mobilePhone" => "+5511992230189",
                    "city" => "Sao Paulo",
                    "addressId" => "",
                    "telephone" => "",
                    "street" => "1302",
                    "district" => "",
                    "name" => "Lucas Sibie Lucas Sibie",
                    "detailAddress" => "Avenida Jandira",
                    "country" => "BR",
                    "countryCode" => "BR",
                    "portCode" => "GRU",
                    "state" => "SP",
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
