<?php

namespace App\Services\Correios\Services\Brazil;

use Exception;
use SoapClient;
use Illuminate\Support\Facades\Log;

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
                'resultado' => 'U',
                'lingua' => 102,
                'objetos' => $trackingNumber,
            );

            $result = $client->buscaEventos($request_param);

            if(isset($result->return->objeto))
            {
                return (Object)[
                    'success' => true,
                    'error' => null,
                    'data'    => $result->return->objeto,
                ];    
            }else{
                return (Object)[
                    'success' => false,
                    'error' => 'no tracking found',
                    'data'    => null,
                ];
            }
            
        }
        catch (Exception $e) {
            Log::info($e->getMessage());
            return (Object)[
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}