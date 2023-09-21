<?php

namespace App\Services\Leve\Models;

class Package extends BaseModel
{
    public $order;
    public $products =[];
    public $recipient;
    public $address;
    public $service_type ='DDU';
    public $auto_confirm = true;
}
