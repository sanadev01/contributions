<?php

namespace App\Services\Sinerlog\Models;

class Package extends BaseModel
{
    public $order;
    public $seller;
    public $selleraddress;
    public $customer;
    public $customershippingaddress;
    public $products =[];
    public $recipient;
    public $service_type ='DDU';
}
