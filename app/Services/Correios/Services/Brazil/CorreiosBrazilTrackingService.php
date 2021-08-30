<?php

namespace App\Services\Correios\Services\Brazil;

use SoapClient;
use Exception;

class CorreiosBrazilTrackingService{

    protected $wsdlUrl;
    protected $user;
    protected $password;

    public function __construct($wsdlUrl, $user, $password)
    {
        $this->wsdlUrl = $wsdlUrl;
        $this->user = $user;
        $this->password = $password;
    }

    public function trackOrder($trackingNumber)
    {
        try
        {
            $client = new SoapClient($this->wsdlUrl, array('trace'=>1));

            $request_param = array(
                'usuario' => $this->user,
                'senha' => $this->password,
                'tipo' => 'L',
                'resultado' => 'T',
                'lingua' => 101,
                'objetos' => $trackingNumber,
            );

            $result = $client->buscaEventos($request_param);

            if(isset($result->return->objeto->evento))
            {
                return (Object)[
                    'success' => true,
                    'error' => null,
                    'data'    => $result->return->objeto->evento,
                ];    
            }
            
        }
        catch (Exception $e) {
            return (Object)[
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}