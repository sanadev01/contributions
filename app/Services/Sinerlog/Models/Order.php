<?php

namespace App\Services\Sinerlog\Models;

class Order extends BaseModel
{
    public $description = null;
    public $orderNumber = null;
    public $externalId = 0;
    public $weight = null;
    public $height = 0;
    public $width = 0;
    public $length = 0;
    public $classification = null;
    public $totalAmount = 0;
    public $currency = "USD";
    public $deliveryType = null;
    public $deliveryTax = 0;
}
