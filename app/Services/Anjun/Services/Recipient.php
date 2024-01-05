<?php

namespace App\Services\Anjun\Services;

use  App\Models\Recipient as OrignalRecipient;

class Recipient
{
    public $fullName;
    public $tax_id;               //Recipient tax or cpf // Brazilian personal tax number.
    public $companyName;          //company name
    public $phone;
    public $mobileNumber;
    public $countryCode;          // like：BR
    public $stateCode;
    public $city;                 //city name
    public $zipcode;
    public $email;
    public $address;

    public function __construct(OrignalRecipient $recipient)
    {
        $this->fullName        = $recipient->first_name . ' ' . $recipient->last_name;
        $this->tax_id          = $recipient->tax_id;
        $this->companyName     = '';
        $this->phone           = $recipient->phone;
        $this->mobileNumber    = '';
        $this->countryCode     = $recipient->country->code;
        $this->stateCode       = $recipient->state->code;
        $this->city            = $recipient->city;
        $this->zipcode         = $recipient->zipcode;
        $this->email           = $recipient->email;
        $this->address         = $recipient->address;
    }
    public function convertToChinese()
    {
        return [
            'contact'  => $this->fullName,
            'tax'      => $this->tax_id,
            'gs'       => $this->companyName,
            'tel'      => $this->phone,
            'sj'       => $this->phone,
            'country'  => $this->countryCode,
            'state'    => $this->stateCode,
            'cs'       => $this->city,
            'yb'       => $this->zipcode,
            'email'    => $this->email,
            'tto'      => $this->address
        ];
    }
}
