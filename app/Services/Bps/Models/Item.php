<?php 

namespace App\Services\Bps\Models;

class Item extends BaseModel{

    public $sh_code;
    public $sku_code;
    public $description;
    public $quantity;
    public $value;
    public $weight;

    /**
     * @var App\Services\Bps\Models\ItemDetails
     */
    public $item_details;
}