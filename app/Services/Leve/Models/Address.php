<?php

namespace App\Services\Leve\Models;

class Address extends BaseModel
{
    public $street = null;
    public $number = 0;
    public $complement = null;
    public $neighborhood = null;
    public $city = null;
    public $zip_code = null;
    public $state_abbreviation = null;
    public $country_abbreviation = "BR";
}
