<?php

namespace App\Services\Correios\Services\Brazil;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Services\Correios\Contracts\Package;
use App\Services\Correios\Models\PackageError;

class CorreiosTrackingService{

    protected $token;
    protected $client;

    public function __construct()
    {
        $authParams = [
            'numero' => '0075745313',
        ];
        
        $response = Http::withBasicAuth('hercofreight', '150495ca')
            ->post('https://api.correios.com.br/token/v1/autentica/cartaopostagem', $authParams);
        
        $data = $response->json(); 
        if (isset($data['token'])) {
            $this->token = $data['token'];
        }
    }

    private function getHeaders()
    {
        return [ 
            'Authorization' => "Bearer {$this->token}",
            'Accept' => 'application/json'
        ];
    }

    public function getTracking($code)
    {
        try {
            $request = Http::withHeaders($this->getHeaders())->get("https://api.correios.com.br/srorastro/v1/objetos/$code?resultado=T");
            $response = json_decode($request);
        
            if (isset($response->objetos) && is_array($response->objetos) && count($response->objetos) > 0) {
                return $response;
            }
        
        } catch (\Exception $exception) {
            return new PackageError($exception->getMessage());
        }
    }

}