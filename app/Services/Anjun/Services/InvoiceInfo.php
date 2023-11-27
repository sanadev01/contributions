<?php
namespace App\Services\Anjun\Services;
use App\Models\OrderItem; 
class InvoiceInfo
{
    public $orderItem       = null; 
    public $chineseName       = null; 
    public $sku               = null; 
    public $hscode            = null;
    public $liquid            = null;
    public $hasBattery        = null; 

    function __construct(OrderItem $orderItem)
    {
        $this->orderItem = $orderItem;
        $this->liquid = $orderItem->contains_flammable_liquid;      
        $this->hasBattery  = $orderItem->contains_battery;    
        $this->chineseName       =   ',牙齿美白笔/剂';
        $this->sku               =   $orderItem->id;
        $this->hscode            =   $orderItem->sh_code;
    }


    public function requestBody()
    {
        return [
                "quantity"=> $this->orderItem->quantity,
                "company"=> "",
                "declaredValue"=> $this->orderItem->value,
                "declaredWeight"=> 0.01,
                "nameCn"=>  $this->chineseName,
                "nameEn"=> "CAP",
                "hscode"=>  $this->hscode,
                "sku"=> $this->sku,
                "purpose"=> "",
                "material"=> "",
                "placeOrigin"=> "CN",
                "brand"=> "OleB",
                "length"=> 0,
                "width"=> 0,
                "height"=> 0,
                "specs"=> "",
                "caseNumber"=> "",
                "distributionInfo"=> "CAP",
                "goodsUrl"=> "https://pt.aliexpress.com/",
                "picturesUrl"=> "https://pt.aliexpress.com/",
                "salesPlatform"=> "",
                "powder"=> 0,
                "liquid"=> $this->liquid,
                "hasBattery"=> $this->hasBattery,
                "batteryType"=> "",
                "magnetic"=> 0
        ];
    }
}
