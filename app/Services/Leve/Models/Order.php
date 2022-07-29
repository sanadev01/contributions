<?php

namespace App\Services\Leve\Models;

class Order extends BaseModel
{
    public $order_number = null;
    public $external_reference = null;
    public $fiscal_number = 0;
    public $purchase_date = null;
    public $weight = 0;
    public $width = 0;
    public $height = 0;
    public $extent_length = 0;
    public $currency = "USD";
    public $shipment_value = 0;
    public $security_price = 0;
    public $mkt_place_name = null;
    public $sender_name = null;
    public $hazardous_contents_labels = [];
}
