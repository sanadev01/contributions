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
                $response = Http::mileExpress()->withHeaders([
                                    'Content-Type' => 'application/json',
                                    'Accept' => 'application/json',
                                ])->acceptJson()->post($this->getTokenUrl, [
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
        return $this->mileExpressApiCall($this->houseUrl, $this->makeRequestBodyForShipment($order));
    }

    private function mileExpressApiCall($url, $data)
    {
        try {
            
            $response = Http::mileExpress()->withHeaders($this->setHeaders())
                                ->acceptJson()->post($url, $data);
            
            return $this->setResponse($response);

        } catch (\Exception $ex) {
            Log::info('MileExpress Api Error ' . $ex->getMessage());
            return (Object)[
                'success' => false,
                'data' => null,
                'error' => $ex->getMessage()
            ];
        }
    }

    private function setResponse($response)
    {
        if ($response->status() == 201) {
            return (Object)[
                'success' => true,
                'data' => $response->json(),
                'error' => null
            ];
        }elseif($response->clientError()) {
            return (Object)[
                'success' => false,
                'data' => null,
                'error' => $response->json()
            ];
        }elseif ($response->status() !== 200) {
            return (Object) [
                'success' => false,
                'data' => null,
                'error' => $response->json(),
            ];
        }
    }

    private function makeRequestBodyForShipment($order)
    {
        return [
            'volumes' => [
                [
                    'height' => '1500',
                    'length' => '200',
                    'width' => '200',
                    'weight' => '3',
                    'items' => $this->setItems($order->items)
                ]
            ],
            'external_identifiers' => [
                $order->warehouse_number
            ],
            'description' => ($order->items->isNotEmpty()) ? $this->setOrderDescription($order->items) : 'goods',
            'amount_freight' => $order->user_declared_freight,
            'gross_weight' => '3',
            'importer' => [
                'name' => $order->recipient->first_name.' '.$order->recipient->last_name,
                'addresses' => [
                    [
                        'number' => 123,
                        'additional_info' => $order->recipient->street_no,
                        'country_code' => 105,
                        'zip_code' => $order->recipient->zipcode,
                        'address' => $order->recipient->address,
                        'city' => [
                            'name' => $order->recipient->city,
                            'uf' => $order->recipient->state->code
                        ],
                        'neighborhood' => 'n'
                    ]
                ],
                'documents' => [
                    [
                        'type' => 'CPF',
                        'number' => $order->recipient->tax_id
                    ]
                ]
            ],
            'shipper' => [
                'name' => 'Herco inc',
                'addresses' => [
                    [
                        'number' => 440,
                        'additional_info' => 'SUITE# 100',
                        'country_code' => 249,
                        'zip_code' => '33182',
                        'address' => '2200 NW 129TH AVE',
                        'city' => [
                            'name' => 'Miami',
                            'uf' => 'FL'
                        ],
                        'neighborhood' => 'N/A'
                    ]
                ],
                'documents' => [
                    'type' => 'CNPJ',
                    'number' => '99.999.999/0001-99'
                ]
            ]
        ];
    }

    private function setItems($items)
    {
        $itemsArr = [];

        foreach ($items as $item) {
            $itemToPush = [];
            $itemToPush = [
                'description' => $item->description,
                'commercial_value' => $item->value,
                'quantity' => $item->quantity,
                'tax_class_code' => '7',
                'customs_adm' => null
            ];

            array_push($itemsArr, $itemToPush);
        }

        return $itemsArr;
    }

    private function setorderDescription($items)
    {
        $itemDescription = [];

        foreach($items as $item)
        {
            array_push($itemDescription, $item->description);
        }

        $description = implode(' ', $itemDescription);
        
        if (strlen($description) > 48){
            $description = str_limit($description, 45);
        }

        return $description;
    }

    private function setHeaders()
    {
        return [
            'Authorization' => 'Bearer ' . $this->getToken(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }
}