<?php

namespace App\Services\Correios\Services\Brazil;

use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client as GuzzleClient;

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
         

        if ($order != null)
            $this->order = $order;
        if ($trackingNumber != null)
            $this->order = Order::where('corrios_tracking_code', strtoupper($trackingNumber))->first();
        $this->client = new GuzzleClient([
            'base_uri' => $this->baseUri
        ]);
        \Log::info([
            'url'=>$this->baseUri,
        ]);
        \Log::info('Shipping Service');
        \Log::info('BCN Service');
        \Log::info($this->order->shippingService->is_bcn_service);
        \Log::info('Anjun Service');
        \Log::info($this->order->shippingService->isAnjunService());
        \Log::info('Corrieos');
        \Log::info($this->order->shippingService->isCorreiosService());

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
        return Cache::remember('bcn_token', Carbon::now()->addHours(0), function () {
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
        $this->order = $this->order->refresh();
        if ($this->order->shippingService->isAnjunService()) {
            Log::info('getAnjunToken');
            return $this->getAnjunToken();
        }
        if ($this->order->shippingService->is_bcn_service) {
            Log::info('getBCNToken');
            return $this->getBCNToken();
        }
        if($this->order->shippingService->isCorreiosService()){
            Log::info('getToken');
            return $this->getToken();
        }
       
    }
}
