<?php 

namespace App\Services\Bps\Models;

class Address extends BaseModel{

    // public $type ='Individual';
    public $number;
    public $address_line_1;
    public $address_line_2;
    public $address_line_3;
    public $state;
    public $city;
    public $postal_code;
    public $country;

}