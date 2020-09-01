<?php 

namespace App\Services\Bps\Models;

class Recipient extends BaseModel{

    public  $type;
    public  $first_name;
    public  $last_name;
    public  $tax_id;
    public  $phone;

    /**
     * @var App\Services\Bps\Models\Address
     */
    public $address;
}