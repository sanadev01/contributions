<?php

namespace App\Services\Anjun\Services;


use App\Services\Anjun\Services\Recipient as AnjunRecipient;
use App\Services\Anjun\Services\Product as AnjunProduct;
use App\Models\Order as OrignalOrder;
use App\Services\Converters\UnitsConverter;

class Package
{
    public $orderId;
    public $totalWeightKG;
    public $totalPriceUSD;
    public $prepaymentMethod;
    public $senderTaxOrVat;
    public $generalCargoOrBatteryTypeCargo;
    public $orderRemarks;
    public $packingRemarks;
    public $recipientInformation;
    public $products = [];

    public function __construct(OrignalOrder $order)
    {
        if ($order->isWeightInKg()) {
            $weight = UnitsConverter::kgToGrams($order->getWeight('kg'));
        } else {
            $kg = UnitsConverter::poundToKg($order->getWeight('lbs'));
            $weight = UnitsConverter::kgToGrams($kg);
        }

        $this->orderId                        =   $order->id;
        $this->totalWeightKG                  =   $weight;
        $this->totalPriceUSD                  =   $order->order_value;
        $this->prepaymentMethod               =   "2";           //default 2 
        $this->senderTaxOrVat                 =   'VAT';         //tax or vat
        $this->generalCargoOrBatteryTypeCargo =   $order->dangrous_goods > 0 ? 1 : 0;
        $this->orderRemarks                   =   'none';
        $this->packingRemarks                 =   'none';
        $this->recipientInformation           =   new AnjunRecipient($order->recipient);

        foreach ($order->items as $orderItem) {
            $this->products[] = (new AnjunProduct($orderItem));
        }
    }

    public function convertToChinese()
    {

        $productsInChinses = [];
        foreach ($this->products as $product) {
            $productsInChinses[] = $product->convertToChinese();
        }
        return [
            "danhao"            => (string) $this->orderId,
            'zzhong'            => (string) $this->totalWeightKG,
            'zprice'            => (string) $this->totalPriceUSD,
            'prepayment_of_vat' => $this->prepaymentMethod,
            's_tax_id'          => $this->senderTaxOrVat,
            'dian'              => $this->generalCargoOrBatteryTypeCargo,
            'bei'               => $this->orderRemarks,
            'title2'            => $this->packingRemarks,
            'address'           => $this->recipientInformation->convertToChinese(),
            'product'           => $productsInChinses,
        ];
    }
}
