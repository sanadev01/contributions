<?php

namespace App\Services\Correios\Models;

use JsonSerializable;

class Sender extends BaseModel{

    public $senderName;
    public $senderAddress;
    public $senderAddressNumber;
    public $senderAddressComplement;
    public $senderZipCode;
    public $senderCityName;
    public $senderState;
    public $senderCountryCode;
    public $senderEmail;
    public $senderWebsite;
}