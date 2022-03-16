<?php

namespace App\Services\FedEx;

use Carbon\Carbon;
use App\Models\ShippingService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Services\Calculators\WeightCalculator;
use App\Services\FedEx\ConsolidatedOrderService;

class FedExService
{
    private $clientId;
    private $clientSecret;
    private $accountNumber;
    private $getTokenUrl;
    private $getRatesUrl;
    private $createShipmentUrl;
    private $createPickupUrl;
    private $cancelPickupUrl;

    private $chargableWeight;

    public function __construct($clientId, $clientSecret, $accountNumber, $getTokenUrl, $getRatesUrl, $createShipmentUrl, $createPickupUrl, $cancelPickupUrl)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->accountNumber = $accountNumber;
        $this->getTokenUrl = $getTokenUrl;
        $this->getRatesUrl = $getRatesUrl;
        $this->createShipmentUrl = $createShipmentUrl;
        $this->createPickupUrl = $createPickupUrl;
        $this->cancelPickupUrl = $cancelPickupUrl;
    }

    private function getToken()
    {
        try {

            return Cache::remember('FedExtoken',Carbon::now()->addMinutes(55),function (){
                $response = Http::asForm()->withHeaders($this->setHeadersForToken())->acceptJson()->post($this->getTokenUrl, $this->setFormParams());

                return $response->successful() ? $response->json()['access_token'] : null;
            });

        } catch (\Exception $ex) {
           Log::info('FedEx Error ' . $ex->getMessage());
            return null;
        }
    }

    public function getRecipientRates($order, $service)
    {
        return $this->fedExApiCall($this->getRatesUrl, $this->makeRatesRequestBodyForRecipient($order, $service));
    }

    public function getSenderRates($order, $request)
    {
        if ($request->exists('consolidated_order') && $request->consolidated_order == false) {
            $consolidatedOrderService = new ConsolidatedOrderService();

            $consolidatedOrderService->handle($this->accountNumber);
            return $this->fedExApiCall($this->getRatesUrl, $consolidatedOrderService->makeRequestForSenderRates($order, $request));
        }

        return $this->fedExApiCall($this->getRatesUrl, $this->makeRatesRequestBodyForSender($order, $request));
    }

    public function createShipmentForSender($order, $request)
    {
        $data = $this->makeShipmentRequestForSender($order, $request);
        return $this->fedExApiCall($this->createShipmentUrl, $data);
    }

    public function createPickupShipment($request)
    {
        return $this->fedExApiCall($this->createPickupUrl, $this->makeRequestBodyForPickup($request));
    }

    public function createShipmentForRecipient($order)
    {
        return $this->fedExApiCall($this->createShipmentUrl, $this->makeShipmentRequestForRecipient($order));
    }

    private function makeRequestBodyForPickup($request)
    {
        return [
            'associatedAccountNumber' => [
                'value' => $this->accountNumber
            ],
            'originDetail' => [
                'pickupLocation' => [
                    'contact' => [
                        'personName' => $request->first_name . ' ' . $request->last_name,
                        'phoneNumber' => $request->sender_phone,
                    ],
                    'address' => [
                        'streetLines' => [$request->sender_address],
                        'city' => $request->sender_city,
                        'stateOrProvinceCode' => $request->sender_state,
                        'postalCode' => $request->sender_zipcode,
                        'countryCode' => 'US',
                    ],
                ],
                'packageLocation' => $request->pickup_location,
                'readyDateTimestamp' => $request->pickup_date.'T'.$request->earliest_pickup_time.':00Z',
                'customerCloseTime' => $request->latest_pickup_time.':00',
            ],
            'carrierCode' => 'FDXG',
        ];
    }

    private function makeRatesRequestBodyForSender($order, $request)
    {
        $this->calculateVolumetricWeight($order);
        $data = [
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
                'pickupType' => ($request->pickupShipment == "true") ? 'USE_SCHEDULED_PICKUP' : 'DROPOFF_AT_FEDEX_LOCATION',
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

        if ($request->pickupShipment == true) {
            $data['requestedShipment']['pickupDetail'] = [
                'companyCloseTime' => $request->latest_pickup_time.':00',
                'pickupOrigin' => [
                    'accountNumber' => [
                        'value' => $this->accountNumber
                    ],
                    'address' => [
                        'city' => $request->sender_city,
                        'stateOrProvinceCode' => $request->sender_state,
                        'postalCode' => $request->sender_zipcode,
                        'countryCode' => 'US',
                        'streetLines' => [$request->sender_address],
                    ],
                ],
            ];
        }
        
        return $data;
    }


    private function makeShipmentRequestForSender($order, $request)
    {
        $this->calculateVolumetricWeight($order);

        return [
            'labelResponseOptions' => 'URL_ONLY',
            'requestedShipment' => [
                'shipper' => [
                    'contact' => [
                        'personName' => $request->first_name.' '.$request->last_name,
                        'phoneNumber' => $request->sender_phone,
                    ],
                    'address' => [
                        'streetLines' => [$request->sender_address],
                        'city' => $request->sender_city,
                        'stateOrProvinceCode' => $request->sender_state,
                        'postalCode' => $request->sender_zipcode,
                        'countryCode' => 'US',
                    ],
                ],
                'recipients' => [
                    [
                        'contact' => [
                            'personName' => 'Marcio Fertias',
                            'phoneNumber' => '+13058885191',
                            'companyName' => 'HERCO SUITE#100'
                        ],
                        'address' => [
                            'streetLines' => ['2200 NW 129TH AVE'],
                            'city' => 'Miami',
                            'stateOrProvinceCode' => 'FL',
                            'postalCode' => 33182,
                            'countryCode' => 'US',
                        ],
                    ]
                ],
                'shipDatestamp' => Carbon::now()->format('Y-m-d'),
                'serviceType' => ($request->service == ShippingService::FEDEX_GROUND) ? 'FEDEX_GROUND' : 'GROUND_HOME_DELIVERY',
                'packagingType' => 'YOUR_PACKAGING',
                'pickupType' => ($request->pickup == "true") ? 'CONTACT_FEDEX_TO_SCHEDULE' : 'DROPOFF_AT_FEDEX_LOCATION',
                'shippingChargesPayment' => [
                    'paymentType' => 'SENDER',
                ],
                'labelSpecification' => [
                    'imageType' => 'PDF',
                    'labelStockType' => 'PAPER_85X11_TOP_HALF_LABEL',
                ],
                'requestedPackageLineItems' => [
                    [
                        'weight' => [
                            'value' => ($this->chargableWeight != null) ? (float)$this->chargableWeight : (float)$order->weight,
                            'units' => ($order->measurement_unit == 'kg/cm') ? 'KG' : 'LB'
                        ]
                    ]
                ],
            ],
            'accountNumber' => [
                'value' => $this->accountNumber,
            ]
        ];
    }

    private function makeRatesRequestBodyForRecipient($order, $service)
    {
        $this->calculateVolumetricWeight($order);
        return [
            'accountNumber' => [
                'value' => $this->accountNumber,
            ],
            'requestedShipment' => [
                'shipper' => [
                    'address' => [
                        'city' => 'Miami',
                        'stateOrProvinceCode' => 'FL',
                        'postalCode' => 33182,
                        'countryCode' => 'US',
                    ]
                ],
                'recipient' => [
                    'address' => [
                        'city' => $order->recipient->city,
                        'stateOrProvinceCode' => $order->recipient->state->code,
                        'postalCode' => $order->recipient->zipcode,
                        'countryCode' => 'US',
                    ]
                ],
                'serviceType' => ($service == ShippingService::FEDEX_GROUND) ? 'FEDEX_GROUND' : 'GROUND_HOME_DELIVERY',
                'pickupType' => 'DROPOFF_AT_FEDEX_LOCATION',
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

    private function makeShipmentRequestForRecipient($order)
    {
        $this->calculateVolumetricWeight($order);
        
        return [
            'labelResponseOptions' => 'URL_ONLY',
            'requestedShipment' => [
                'shipper' => [
                    'contact' => [
                        'personName' => 'Marcio Fertias',
                        'phoneNumber' => '+13058885191',
                        'companyName' => 'HERCO SUITE#100'
                    ],
                    'address' => [
                        'streetLines' => ['2200 NW 129TH AVE'],
                        'city' => 'Miami',
                        'stateOrProvinceCode' => 'FL',
                        'postalCode' => 33182,
                        'countryCode' => 'US',
                    ],
                ],
                'recipients' => [
                    [
                        'contact' => [
                            'personName' => $order->recipient->first_name.' '.$order->recipient->last_name,
                            'phoneNumber' => $order->recipient->phone,
                        ],
                        'address' => [
                            'streetLines' => [$order->recipient->address.' '.$order->recipient->street_no],
                            'city' => $order->recipient->city,
                            'stateOrProvinceCode' => $order->recipient->state->code,
                            'postalCode' => $order->recipient->zipcode,
                            'countryCode' => 'US',
                        ],
                    ]
                ],
                'shipDatestamp' => Carbon::now()->format('Y-m-d'),
                'serviceType' => ($order->shippingService->service_sub_class == ShippingService::FEDEX_GROUND) ? 'FEDEX_GROUND' : 'GROUND_HOME_DELIVERY',
                'packagingType' => 'YOUR_PACKAGING',
                'pickupType' => 'DROPOFF_AT_FEDEX_LOCATION',
                'shippingChargesPayment' => [
                    'paymentType' => 'SENDER',
                ],
                'labelSpecification' => [
                    'imageType' => 'PDF',
                    'labelStockType' => 'PAPER_85X11_TOP_HALF_LABEL',
                ],
                'requestedPackageLineItems' => [
                    [
                        'weight' => [
                            'value' => ($this->chargableWeight != null) ? (float)$this->chargableWeight : (float)$order->weight,
                            'units' => ($order->measurement_unit == 'kg/cm') ? 'KG' : 'LB'
                        ]
                    ]
                ],
            ],
            'accountNumber' => [
                'value' => $this->accountNumber,
            ]
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
           Log::info('FedEx Error ' . $ex->getMessage());
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
