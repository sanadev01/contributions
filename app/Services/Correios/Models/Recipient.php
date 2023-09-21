<?php

namespace App\Services\Correios\Models;

class Recipient extends BaseModel{

    public  $recipientName; 
    public  $recipientDocumentType = "CPF";
    public  $recipientDocumentNumber;
    public  $recipientAddress;
    public  $recipientAddressNumber;
    public  $recipientAddressComplement;
    public  $recipientCityName;
    public  $recipientState;
    public  $recipientZipCode;
    public  $recipientEmail;
    public  $recipientPhoneNumber;
    public  $recipientCountry;
}