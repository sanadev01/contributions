<?php

namespace App\Services\FedEx;

class FedExService
{
    private $clientId;
    private $clientSecret;
    private $getTokenUrl;
    private $getRatesUrl;
    private $createShipmentUrl;

    public function __construct($clientId, $clientSecret, $getTokenUrl, $getRatesUrl, $createShipmentUrl)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->getTokenUrl = $getTokenUrl;
        $this->getRatesUrl = $getRatesUrl;
        $this->createShipmentUrl = $createShipmentUrl;
    }
}
