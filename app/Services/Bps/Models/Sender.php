<?php 

namespace App\Services\Bps\Models;

class Sender extends BaseModel{

    public $type;
    public $first_name;
    public $last_name;
    public $company_name;
    public $tax_id;
    public $email;
    public $phone;
    public $website;

    /**
     * @var App\Services\Bps\Models\Address
     */
    public $address;
}