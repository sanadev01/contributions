<?php

namespace App\Services\Correios\Services\Brazil;

use GuzzleHttp\Client as GuzzleClient;
use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Support\Facades\Cache;

class GetServiceToken
{

    private $order;
    private $username = '';
    private $password = '';
    private $numero = '';

    private $anjun_username = '';
    private $anjun_password = '';
    private $anjun_numero = '';

    private $bcn_username = '';
    private $bcn_password = '';
    private $bcn_numero = '';
    private $baseUri = '';

    protected $client;

    function __construct($order = null, $trackingNumber = null)
    {
        if(app()->isProduction()){ 
                $this->baseUri = 'https://api.correios.com.br'; 
                $this->username = 'hercofreight';
                $this->password = '150495ca';
                $this->numero = '0075745313';                
                //anjun credentilas
                $this->anjun_username = 'anjun2020';
                $this->anjun_password = 'anjun';
                $this->anjun_numero = '0077053850';                
                //bcn credentials
                $this->bcn_username = '37148594000192';
                $this->bcn_password = '9wdkSYsvk2FkqNbojC1CLlUhN1RY3HqqmmADFBPa';
                $this->bcn_numero = '0076204456'; 
        }else{
                $this->baseUri = 'https://apihom.correios.com.br';
                
                $this->username =$this->anjun_username =  $this->bcn_username = 'testeint';
                $this->password =  $this->anjun_password =  $this->bcn_password ='sUKDOgmLQaoXgTGDsNveWpnf1KhqEEjeAn2U3Ts4';
                $this->numero =$this->anjun_numero =  $this->bcn_numero ='0076772055'; 
                
        }

        if ($order != null)
            $this->order = $order;
        if ($trackingNumber != null)
            $this->order = Order::where('corrios_tracking_code', strtoupper($trackingNumber))->first();
        $this->client = new GuzzleClient([
            'base_uri' => $this->baseUri
        ]);
    }

    public function getToken()
    {
        return Cache::remember('token', Carbon::now()->addHours(24), function () {
            $response = $this->client->post('/token/v1/autentica/cartaopostagem', [
                'auth' => [
                    $this->username,
                    $this->password
                ],
                'json' => [
                    'numero' => $this->numero
                ]
            ]);

            return $response->getStatusCode() == 201 ? "Bearer " . optional(json_decode($response->getBody()->getContents()))->token : null;
        });
    }

    public function getAnjunToken()
    {
        return Cache::remember('anjun_token', Carbon::now()->addHours(0), function () {
            $response = $this->client->post('/token/v1/autentica/cartaopostagem', [
                'auth' => [
                    $this->anjun_username,
                    $this->anjun_password
                ],
                'json' => [
                    'numero' => $this->anjun_numero
                ]
            ]);

            return $response->getStatusCode() == 201 ? "Bearer " . optional(json_decode($response->getBody()->getContents()))->token : null;
        });
    }
    public function getBCNToken()
    {
        return Cache::remember('bcn_token_t', Carbon::now()->addHours(0), function () {
            $response = $this->client->post('/token/v1/autentica/cartaopostagem', [
                'auth' => [
                    $this->bcn_username,
                    $this->bcn_password
                ],
                'json' => [
                    'numero' => $this->bcn_numero
                ]
            ]);

            return $response->getStatusCode() == 201 ? "Bearer " . optional(json_decode($response->getBody()->getContents()))->token : null;
        });
    }
    public function getBearerToken()
    {
        if ($this->order instanceof Order) {
            if ($this->order->shippingService->isAnjunService()) {
                return $this->getAnjunToken();
            } elseif ($this->order->shippingService->is_bcn_service) {
                return $this->getBCNToken();
            } else {
                return $this->getToken();
            }
        } else {
            if ($this->order->hasAnjunService()) {
                return $this->getAnjunToken();
            } elseif ($this->order->hasBCNService()) {
                return $this->getBCNToken();
            } else {
                return $this->getToken();
            }
        }
    }
}
