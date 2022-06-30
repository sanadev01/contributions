<?php

namespace App\Services\MileExpress;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MileExpressService
{
    private $clientId;
    private $clientSecret;
    private $userName;
    private $password;
    private $getTokenUrl;
    private $houseUrl;
    private $trackingUrl;

    public function __construct($clientId, $clientSecret, $userName, $password, $getTokenUrl, $houseUrl, $trackingUrl)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->userName = $userName;
        $this->password = $password;

        $this->getTokenUrl = $getTokenUrl;
        $this->houseUrl = $houseUrl;
        $this->trackingUrl = $trackingUrl;
    }

    private function getToken()
    {
        try {
            
            return Cache::remember('MileExpressToken', Carbon::now()->addMinutes(55), function(){
                $response = Http::mileExpress()->withHeaders($this->setHeadersForToken())
                                    ->acceptJson()->post($this->getTokenUrl, [
                                        'grant_type' => 'password',
                                        'client_id' => $this->clientId,
                                        'client_secret' => $this->clientSecret,
                                        'username' => $this->userName,
                                        'password' => $this->password,
                                        'scope' => '*'
                                    ]);

                return $response->successful() ? $response->json()['access_token'] : null;                  
            });

        } catch (\Exception $ex) {
            
            Log::info('MileExpress Token Error ' . $ex->getMessage());
            return null;
        }
    }

    public function createShipment($order)
    {
        return null;
    }

    private function setHeadersForToken()
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }
}