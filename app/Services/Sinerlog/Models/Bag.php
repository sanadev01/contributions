<?php

namespace App\Services\Sinerlog\Models;

class Bag extends BaseModel
{
    public $bag_code = '';
    public $seal_barcode = '';
    public $unitization_type = 0;
    public $weight = 0;
    public $orders = [];
}
