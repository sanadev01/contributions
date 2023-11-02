<?php

namespace App\Services\Correios\Services\Brazil;

use GuzzleHttp\Client as GuzzleClient;
use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Support\Facades\Cache;

class GetServiceToken
{

    private $order;

    private $username = 'hercofreight';
    private $password = '150495ca';
    private $numero = '0075745313';

    private $anjun_username = 'anjun2020';
    private $anjun_password = 'anjun';
    private $anjun_numero = '0077053850';

    private $bcn_username = '37148594000192';
    private $bcn_password = '9wdkSYsvk2FkqNbojC1CLlUhN1RY3HqqmmADFBPa';
    private $bcn_numero = '0076204456';
    private $baseUri = 'https://api.correios.com.br';

    protected $client;

    function __construct($order = null, $trackingNumber = null)
    {
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
        dd(3);
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
                return $this->order->getAnjunToken();
            } elseif ($this->order->hasBCNService()) {
                return $this->getBCNToken();
            } else {
                return $this->getToken();
            }
        }
    }
}
