<?php

namespace App\Services\FedEx;

use Carbon\Carbon;
use App\Models\ShippingService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Services\Calculators\WeightCalculator;

class FedExService
{
    private $clientId;
    private $clientSecret;
    private $accountNumber;
    private $getTokenUrl;
    private $getRatesUrl;
    private $createShipmentUrl;

    private $chargableWeight;

    public function __construct($clientId, $clientSecret, $accountNumber, $getTokenUrl, $getRatesUrl, $createShipmentUrl)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->accountNumber = $accountNumber;
        $this->getTokenUrl = $getTokenUrl;
        $this->getRatesUrl = $getRatesUrl;
        $this->createShipmentUrl = $createShipmentUrl;
    }

    private function getToken()
    {
        try {

            return Cache::remember('FedExtoken',Carbon::now()->addMinutes(55),function (){
                $response = Http::asForm()->withHeaders($this->setHeadersForToken())->acceptJson()->post($this->getTokenUrl, $this->setFormParams());

                return $response->successful() ? $response->json()['access_token'] : null;
            });

        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return null;
        }
    }

    public function getSenderRates($order, $request)
    {
       $data = $this->makeRatesRequestBodyForSenderRates($order, $request);
       return $this->fedExApiCall($this->getRatesUrl, $data);
    }

    private function makeRatesRequestBodyForSenderRates($order, $request)
    {
        $this->calculateVolumetricWeight($order);
        return [
            'accountNumber' => [
                'value' => $this->accountNumber,
            ],
            'requestedShipment' => [
                'shipper' => [
                    'address' => [
                        'city' => $request->sender_city,
                        'stateOrProvinceCode' => $request->sender_state,
                        'postalCode' => $request->sender_zipcode,
                        'countryCode' => 'US',
                    ]
                ],
                'recipient' => [
                    'address' => [
                        'city' => 'Miami',
                        'stateOrProvinceCode' => 'FL',
                        'postalCode' => 33182,
                        'countryCode' => 'US',
                    ]
                ],
                'serviceType' => ($request->service == ShippingService::FEDEX_GROUND) ? 'FEDEX_GROUND' : 'GROUND_HOME_DELIVERY',
                'pickupType' => ($request->pickup == "true") ? 'CONTACT_FEDEX_TO_SCHEDULE' : 'DROPOFF_AT_FEDEX_LOCATION',
                'rateRequestType' => [
                    'ACCOUNT'
                ],
                'requestedPackageLineItems' => [
                    [
                        'weight' => [
                            'units' => ($order->measurement_unit == 'kg/cm') ? 'KG' : 'LB',
                            'value' => ($this->chargableWeight != null) ? (float)$this->chargableWeight : (float)$order->weight
                        ]
                    ]
                ]
            ],
        ];
    }

    public function calculateVolumetricWeight($order)
    {
        if ( $order->measurement_unit == 'kg/cm' ){

            $volumetricWeight = WeightCalculator::getVolumnWeight($order->length,$order->width,$order->height,'cm');
            return $this->chargableWeight = round($volumetricWeight >  $order->weight ? $volumetricWeight :  $order->weight,2);

        }else{

            $volumetricWeight = WeightCalculator::getVolumnWeight($order->length,$order->width,$order->height,'in');
           return $this->chargableWeight = round($volumetricWeight >  $order->weight ? $volumetricWeight :  $order->weight,2);
        }
    }

    private function fedExApiCall($url, $data)
    {
        try {
            
            $response = Http::withHeaders($this->setHeaders())->acceptJson()->post($url, $data);
            return $this->setResponse($response);

        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return (object) [
                'success' => false,
                'error' => $ex->getMessage(),
            ];
        }
    }
    
    private function setHeaders()
    {
       return [
            'Authorization' => 'Bearer '.$this->getToken(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    public function setResponse($response)
    {
        if($response->successful())
        {
            return (Object)[
                'success' => true,
                'data' => $response->json(),
                'error' => null,
            ];
        }elseif($response->clientError())
        {
            return (Object)[
                'success' => false,
                'error' => $response->json(),
            ];    
        }elseif ($response->status() !== 200) 
        {

            return (object) [
                'success' => false,
                'error' => $response->json(),
            ];
        }
    }

    private function setHeadersForToken()
    {
        return [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Accept' => 'application/json',
        ];
    }

    private function setFormParams()
    {
        return [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
        ];
    }
}
