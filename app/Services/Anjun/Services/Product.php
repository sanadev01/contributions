<?php
namespace App\Services\Anjun\Services;
use App\Models\OrderItem; 
class Product
{
    public $chineseName       = null;
    public $englishName       = null;
    public $SKU               = null;
    public $productQuantity   = null;
    public $productPictureURL = '';
    public $productSalesURL   = null;
    public $productWeightKG   = null;
    public $price             = null;
    public $productMaterial   = null;
    public $productUse        = null;
    public $productCustomCode = null;
    public $hscode            = null;

    function __construct(OrderItem $orderItem)
    {
        $this->chineseName       =   '虚拟名称';
        $this->englishName       =   $orderItem->description;
        $this->SKU               =   $orderItem->id;
        $this->productQuantity   =   $orderItem->quantity;
        $this->productPictureURL =   '';
        $this->productSalesURL   =   '';
        $this->productWeightKG   =   '';
        $this->price             =   $orderItem->value;
        $this->productMaterial   =   '';
        $this->productUse        =   '';
        $this->hscode            =   $orderItem->sh_code;
    }


    public function convertToChinese()
    {
        return [
            "productnamecn"          =>      $this->chineseName,
            "productnameen"          =>      $this->englishName,
            "sku"                    =>      $this->SKU,
            "productqantity"         =>      $this->productQuantity,
            "productpic"             =>      $this->productPictureURL,
            "producturl"             =>      $this->productSalesURL,
            "weight"                 =>      $this->productWeightKG,
            "price"                  =>      $this->price,
            "productspecifications"  =>      $this->productMaterial,
            "use"                    =>      $this->productUse,
            "hscode"                 =>      $this->hscode, 
        ];
    }
}
