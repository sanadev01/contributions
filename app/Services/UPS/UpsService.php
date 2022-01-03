<?php
namespace App\Services\UPS;

use Exception;
use App\Models\ShippingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Services\Converters\UnitsConverter;
use App\Services\Calculators\WeightCalculator;

class UpsService
{
    protected $create_package_url;
    protected $delete_package_url;
    protected $create_manifest_url;
    protected $rating_package_url;
    protected $pickup_rating_url;
    protected $pickup_shipment_url;
    protected $pickup_cancel_url;
    protected $tracking_url;
    protected $userName;
    protected $password;
    protected $transactionSrc;
    protected $chargableWeight;
    protected $shipperNumber;
    protected $AccessLicenseNumber;
    protected $itemDescription = [];

    protected $width;
    protected $height;
    protected $length;
    protected $weight;

    public function __construct($create_package_url, $delete_package_url, $create_manifest_url, $rating_package_url, $pickup_rating_url, $pickup_shipment_url, $pickup_cancel_url, $tracking_url, $transactionSrc, $userName, $password, $shipperNumber, $AccessLicenseNumber)
    {
        $this->create_package_url = $create_package_url;
        $this->delete_usps_label_url = $delete_package_url;
        $this->create_manifest_url = $create_manifest_url;
        $this->rating_package_url = $rating_package_url;
        $this->pickup_rating_url = $pickup_rating_url;
        $this->pickup_shipment_url = $pickup_shipment_url;
        $this->pickup_cancel_url = $pickup_cancel_url;
        $this->tracking_url = $tracking_url;
        $this->userName = $userName;
        $this->password = $password;
        $this->transactionSrc = $transactionSrc;
        $this->shipperNumber = $shipperNumber;
        $this->AccessLicenseNumber = $AccessLicenseNumber;
    }

    public function generateLabel($order)
    {
        $data = $this->make_package_request_for_recipient($order);
        return $this->ups_ApiCall($this->create_package_url, $data);
    }

    public function getSenderPrice($order, $request_data)
    {   
       $data = $this->make_rates_request_for_sender($order, $request_data);
        
       return $this->ups_ApiCall($this->rating_package_url, $data);
    }

    public function buyLabel($order, $request_sender_data)
    {
        $data = $this->make_package_request_for_sender($order, $request_sender_data);
        
        return $this->ups_ApiCall($this->create_package_url, $data);
    }

    public function getRecipientRates($order, $service)
    {
        $data = $this->make_rates_request_for_recipient($order, $service);

        return $this->ups_ApiCall($this->rating_package_url, $data);
    }

    public function getPickupRates($request)
    {
        $data = $this->make_request_for_pickup_rates($request);
        return $this->upsApiCallForPickup($this->pickup_rating_url, $data);
    }

    public function createPickupShipment($order, $request)
    {
       $data = $this->make_request_for_pickup_shipment($order, $request);
       return $this->upsApiCallForPickup($this->pickup_shipment_url, $data);
    }

    public function cancelPickup($prn)
    {
        return $this->cancelUPSPickup($prn);
    }

    public function trackOrder($trackingNumber)
    {
        return $this->trackUPSOrder($trackingNumber);
    }

    private function make_rates_request_for_sender($order, $request)
    {   
        $this->calculateVolumetricWeight($order);

        $request_body = [
            'RateRequest' => [
                'Request' => [
                    'SubVersion' => '1703',
                    'TransactionReference' => [
                        'CustomerContext' => ''
                    ]
                ],
                'CustomerClassification' => [
                    'Code' => '01'
                ],
                'Shipment' => [
                    'Shipper' => [
                        'Name' => Auth::user() ? Auth::user()->pobox_number :  'HERCO SUITE#100',
                        'AttentionName' => ($request->first_name) ? $request->first_name : 'HERCO SUITE#100',
                        'ShipperNumber' => $this->shipperNumber,
                        'Phone' => [
                            'Number' => Auth::user() ? Auth::user()->phone : '+13058885191'
                        ],
                        'Address' => [
                            'AddressLine' => $request->sender_address,
                            'City' => $request->sender_city,
                            'StateProvinceCode' => $request->sender_state,
                            'PostalCode' => $request->sender_zipcode,
                            'CountryCode' => 'US',
                        ],
                    ],
                    'ShipFrom' => [
                        'Name' => ($request->first_name) ? $request->first_name : 'HERCO SUITE#100',
                        'Address' => [
                            'AddressLine' => $request->sender_address,
                            'City' => $request->sender_city,
                            'StateProvinceCode' => $request->sender_state,
                            'PostalCode' => $request->sender_zipcode,
                            'CountryCode' => 'US',
                        ],
                    ],
                    'ShipTo' => [
                        'Name' => 'HERCO SUITE#100',
                        'Address' => [
                            'AddressLine' => '2200 NW 129TH AVE',
                            'City' => 'Miami',
                            'StateProvinceCode' => 'FL',
                            'PostalCode' => '33182',
                            'CountryCode' => 'US',
                        ],
                    ],
                    'PaymentDetails' => $this->getPaymentDetails(),
                    'Service' =>  [
                        'Code' => '0'.$request->service,
                        'Description' => 'Ground Service'
                    ],
                    'ShipmentTotalWeight' => $this->getShipmentTotalWeight(),
                    'Package' => [
                        'PackagingType' => [
                            'Code' => '02',
                            'Description' => 'Package'
                        ],
                        'Dimensions' => [
                            'UnitOfMeasurement' => [
                                'Code' => 'IN',
                            ],
                            'Length' => ($order->measurement_unit == 'kg/cm') ? "$this->length" :"$order->length",
                            'Width' => ($order->measurement_unit == 'kg/cm') ? "$this->width" : "$order->width",
                            'Height' => ($order->measurement_unit == 'kg/cm') ? "$this->height" : "$order->height",
                        ],
                        'PackageWeight' => [
                            'UnitOfMeasurement' => [
                                'Code' => 'LBS',
                            ],
                            'Weight' => ($this->chargableWeight != null) ? "$this->chargableWeight" : (($order->measurement_unit == 'kg/cm') ? "$this->weight" :"$order->weight"),
                        ],
                        'ShipmentServiceOptions' => [
                            'DirectDeliveryOnlyIndicator' => '1'
                        ],
                    ],
                ],
            ],
        ];

        return $request_body;
    }

    private function make_package_request_for_sender($order, $request)
    {
        $this->calculateVolumetricWeight($order);

        $request_body = [
            'ShipmentRequest' => [
                'Shipment' => [
                    'Description' => $order->items->count() > 0 ? $this->orderDescription($order->items) : 'goods',
                    'Shipper' => [
                        'Name' => Auth::user() ? Auth::user()->pobox_number.' - WRH#: '.$order->warehouse_number :  optional($order->user)->pobox_number.' - WRH#: '.$order->warehouse_number,
                        'AttentionName' => $request->first_name.' '.$request->last_name,
                        'ShipperNumber' => $this->shipperNumber,
                        'Phone' => [
                            'Number' => Auth::user() ? Auth::user()->phone : '+13058885191'
                        ],
                        'Address' => [
                            'AddressLine' => $request->sender_address,
                            'City' => $request->sender_city,
                            'StateProvinceCode' => $request->sender_state,
                            'PostalCode' => $request->sender_zipcode,
                            'CountryCode' => 'US',
                        ],
                    ],
                    'ShipTo' => [
                        'Name' => 'HERCO SUITE#100',
                        'AttentionName' => Auth::user() ? Auth::user()->pobox_number :  optional($order->user)->pobox_number,
                        'Phone' => [
                            'Number' => '+13058885191'
                        ],
                        'Address' => [
                            'AddressLine' => '2200 NW 129TH AVE',
                            'City' => 'Miami',
                            'StateProvinceCode' => 'FL',
                            'PostalCode' => '33182',
                            'CountryCode' => 'US',
                        ],
                    ],
                    'ShipFrom' => [
                        'Name' => $request->first_name.' '.$request->last_name,
                        'AttentionName' => $request->first_name.' '.$request->last_name,
                        'Address' => [
                            'AddressLine' => $request->sender_address,
                            'City' => $request->sender_city,
                            'StateProvinceCode' => $request->sender_state,
                            'PostalCode' => $request->sender_zipcode,
                            'CountryCode' => 'US',
                        ],
                        'Phone' => [
                            'Number' => $request->sender_phone,
                        ],
                    ],
                    'PaymentInformation' => $this->getPaymentDetails(),
                    'Service' => [
                        'Code' => '0'.$request->service,
                        'Description' => 'Ground Service'
                    ],
                    'Package' => [
                        [
                            'Description' => $order->items->count() > 0 ? $this->orderDescription($order->items) : 'goods',
                            'Packaging' => [
                                'Code' => '02',
                                'Description' => 'Customer Supplied Package'
                            ],
                            'Dimensions' => [
                                'UnitOfMeasurement' => [
                                    'Code' => 'IN',
                                ],
                                'Length' => ($order->measurement_unit == 'kg/cm') ? "$this->length" :"$order->length",
                                'Width' => ($order->measurement_unit == 'kg/cm') ? "$this->width" : "$order->width",
                                'Height' => ($order->measurement_unit == 'kg/cm') ? "$this->height" : "$order->height",
                            ],
                            'PackageWeight' => [
                                'UnitOfMeasurement' => [
                                    'Code' => 'LBS',
                                ],
                                'Weight' => ($this->chargableWeight != null) ? "$this->chargableWeight" : (($order->measurement_unit == 'kg/cm') ? "$this->weight" :"$order->weight"),
                            ],
                        ]
                    ],
                    'ShipmentServiceOptions' => [
                        'DirectDeliveryOnlyIndicator' => '1'
                    ],
                    'ItemizedChargesRequestedIndicator' => '1',
                    'RatingMethodRequestedIndicator' => '1',
                    'TaxInformationIndicator' => '1',
                    'ShipmentRatingOptions' => [
                        'NegotiatedRatesIndicator' => '1'
                    ],
                ],
                'LabelSpecification' => [
                    'LabelImageFormat' => [
                        'Code' => 'PNG',
                    ]
                ]
            ],
        ];

        return $request_body;
    }

    private function make_rates_request_for_recipient($order, $service)
    {
        $this->calculateVolumetricWeight($order);

        $request_body = [
            'RateRequest' => [
                'Request' => [
                    'SubVersion' => '1703',
                    'TransactionReference' => [
                        'CustomerContext' => ''
                    ]
                ],
                'CustomerClassification' => [
                    'Code' => '01'
                ],
                'Shipment' => [
                    'Shipper' => [
                        'Name' => Auth::user() ? Auth::user()->pobox_number :  'HERCO SUITE#100',
                        'AttentionName' => $order->sender_first_name.' '.$order->sender_last_name,
                        'ShipperNumber' => $this->shipperNumber,
                        'Phone' => [
                            'Number' => Auth::user() ? Auth::user()->phone : '+13058885191'
                        ],
                        'Address' => [
                            'AddressLine' => $order->sender_address,
                            'City' => $order->sender_city,
                            'StateProvinceCode' => optional($order->senderState)->code,
                            'PostalCode' => $order->sender_zipcode,
                            'CountryCode' => 'US',
                        ],
                    ],
                    'ShipFrom' => [
                        'Name' => 'HERCO SUITE#100',
                        'Address' => [
                            'AddressLine' => '2200 NW 129TH AVE',
                            'City' => 'Miami',
                            'StateProvinceCode' => 'FL',
                            'PostalCode' => '33182',
                            'CountryCode' => 'US',
                        ],
                    ],
                    'ShipTo' => [
                        'Name' => $order->recipient->first_name.' '.$order->recipient->last_name,
                        'Address' => [
                            'AddressLine' => $order->recipient->address.' '.$order->recipient->street_no,
                            'City' => $order->recipient->city,
                            'StateProvinceCode' => $order->recipient->state->code,
                            'PostalCode' => $order->recipient->zipcode,
                            'CountryCode' => 'US',
                        ],
                    ],
                    'PaymentDetails' => $this->getPaymentDetails(),
                    'Service' =>  [
                        'Code' => '0'.$service,
                        'Description' => 'Ground Service'
                    ],
                    'ShipmentTotalWeight' => $this->getShipmentTotalWeight(),
                    'Package' => [
                        'PackagingType' => [
                            'Code' => '02',
                            'Description' => 'Package'
                        ],
                        'Dimensions' => [
                            'UnitOfMeasurement' => [
                                'Code' => 'IN',
                            ],
                            'Length' => ($order->measurement_unit == 'kg/cm') ? "$this->length" :"$order->length",
                            'Width' => ($order->measurement_unit == 'kg/cm') ? "$this->width" : "$order->width",
                            'Height' => ($order->measurement_unit == 'kg/cm') ? "$this->height" : "$order->height",
                        ],
                        'PackageWeight' => [
                            'UnitOfMeasurement' => [
                                'Code' => 'LBS',
                            ],
                            'Weight' => ($this->chargableWeight != null) ? "$this->chargableWeight" : (($order->measurement_unit == 'kg/cm') ? "$this->weight" :"$order->weight"),
                        ],
                        'ShipmentServiceOptions' => [
                            'DirectDeliveryOnlyIndicator' => '1'
                        ],
                    ],
                ],
            ],
        ];

        return $request_body;
    }

    private function make_package_request_for_recipient($order)
    {
        $this->calculateVolumetricWeight($order);

        $request_body = [
            'ShipmentRequest' => [
                'Shipment' => [
                    'Description' => $this->orderDescription($order->items),
                    'Shipper' => [
                        'Name' => optional($order->user)->pobox_number.' - WRH#: '.$order->warehouse_number,
                        'AttentionName' => $order->sender_first_name.' '.$order->sender_last_name,
                        'ShipperNumber' => $this->shipperNumber,
                        'Phone' => [
                            'Number' => $order->sender_phone ? $order->sender_phone : '+13058885191'
                        ],
                        'Address' => [
                            'AddressLine' => $order->sender_address,
                            'City' => $order->sender_city,
                            'StateProvinceCode' => optional($order->senderState)->code,
                            'PostalCode' => $order->sender_zipcode,
                            'CountryCode' => 'US',
                        ],
                    ],
                    'ShipFrom' => [
                        'Name' => $order->sender_first_name.' '.$order->sender_last_name,
                        'AttentionName' => $order->sender_first_name.' '.$order->sender_last_name,
                        'Phone' => [
                            'Number' => $order->sender_phone ? $order->sender_phone : '+13058885191'
                        ],
                        'Address' => [
                            'AddressLine' => $order->sender_address,
                            'City' => $order->sender_city,
                            'StateProvinceCode' => optional($order->senderState)->code,
                            'PostalCode' => $order->sender_zipcode,
                            'CountryCode' => 'US',
                        ],
                    ],
                    'ShipTo' => [
                        'Name' => $order->recipient->first_name.' '.$order->recipient->last_name,
                        'AttentionName' => optional($order->user)->pobox_number,
                        'Address' => [
                            'AddressLine' => $order->recipient->address.' '.$order->recipient->street_no,
                            'City' => $order->recipient->city,
                            'StateProvinceCode' => $order->recipient->state->code,
                            'PostalCode' => $order->recipient->zipcode,
                            'CountryCode' => 'US',
                        ],
                        'Phone' => [
                            'Number' => $order->recipient->phone,
                        ],
                    ],
                    'PaymentInformation' => $this->getPaymentDetails(),
                    'Service' => [
                        'Code' => '0'.$order->shippingService->service_sub_class,
                        'Description' => 'Ground Service'
                    ],
                    'Package' => [
                        [
                            'Description' => $this->orderDescription($order->items),
                            'Packaging' => [
                                'Code' => '02',
                                'Description' => 'Customer Supplied Package'
                            ],
                            'Dimensions' => [
                                'UnitOfMeasurement' => [
                                    'Code' => 'IN',
                                ],
                                'Length' => ($order->measurement_unit == 'kg/cm') ? "$this->length" :"$order->length",
                                'Width' => ($order->measurement_unit == 'kg/cm') ? "$this->width" : "$order->width",
                                'Height' => ($order->measurement_unit == 'kg/cm') ? "$this->height" : "$order->height",
                            ],
                            'PackageWeight' => [
                                'UnitOfMeasurement' => [
                                    'Code' => 'LBS',
                                ],
                                'Weight' => ($this->chargableWeight != null) ? "$this->chargableWeight" : (($order->measurement_unit == 'kg/cm') ? "$this->weight" :"$order->weight"),
                            ],
                        ]
                    ],
                    'ShipmentServiceOptions' => [
                        'DirectDeliveryOnlyIndicator' => '1'
                    ],
                    'ItemizedChargesRequestedIndicator' => '1',
                    'RatingMethodRequestedIndicator' => '1',
                    'TaxInformationIndicator' => '1',
                    'ShipmentRatingOptions' => [
                        'NegotiatedRatesIndicator' => '1'
                    ],
                ],
                'LabelSpecification' => [
                    'LabelImageFormat' => [
                        'Code' => 'PNG',
                    ]
                ]
            ],
        ];

        return $request_body;
    }

    private function make_request_for_pickup_rates($request)
    {
        $request_body = [
            'PickupRateRequest' => [
                'ShipperAccount' => [
                    'AccountNumber' => $this->shipperNumber,
                    'AccountCountryCode' => 'US'
                ],
                'PickupAddress' => [
                    'AddressLine' => $request->sender_address,
                    'City' => $request->sender_city,
                    'StateProvinceCode' => $request->sender_state,
                    'PostalCode' => $request->sender_zipcode,
                    'CountryCode' => 'US',
                    'ResidentialIndicator' => 'Y'
                ],
                'AlternateAddressIndicator' => 'N',
                'ServiceDateOption' => '02',
                'PickupDateInfo' => [
                    'PickupDate' => date('y-m-d', strtotime($request->pickup_date)),
                    'ReadyTime' => $request->earliest_pickup_time,
                    'CloseTime' => $request->latest_pickup_time
                ]
            ]
        ];

        return $request_body;
    }

    private function make_request_for_pickup_shipment($order, $request)
    {
        $this->calculateVolumetricWeight($order);

        $request_body = [
            'PickupCreationRequest' => [
                'RatePickupIndicator' => 'Y',
                'Shipper' => [
                    'Account' => [
                        'AccountNumber' => $this->shipperNumber,
                        'AccountCountryCode' => 'US'
                    ],
                ],
                'PickupDateInfo' => [
                    'PickupDate' => '20'.str_replace("-", "", date('y-m-d', strtotime($request->pickup_date))),
                    'ReadyTime' => str_replace(":", "",$request->earliest_pickup_time),
                    'CloseTime' => str_replace(":", "",$request->latest_pickup_time)
                ],
                'PickupAddress' => [
                    'CompanyName' => $request->first_name.' '.$request->last_name,
                    'ContactName' => $request->first_name.' '.$request->last_name,
                    'AddressLine' => $request->sender_address,
                    'City' => $request->sender_city,
                    'StateProvinceCode' => $request->sender_state,
                    'PostalCode' => $request->sender_zipcode,
                    'CountryCode' => 'US',
                    'ResidentialIndicator' => 'Y',
                    'PickupPoint' => $request->pickup_location,
                    'Phone' => [
                        'Number' => $order->user->phone
                    ]
                ],
                'AlternateAddressIndicator' => 'N',
                'PickupPiece' => [
                    [
                        'ServiceCode' => '00'.$request->service,
                        'Quantity' => '1',
                        'DestinationCountryCode' => 'US',
                        'ContainerCode' => '01',
                    ]

                    ],
                    'TotalWeight' => [
                        'Weight' => ($this->chargableWeight != null) ? "$this->chargableWeight" : (($order->measurement_unit == 'kg/cm') ? "$this->weight" :"$order->weight"),
                        'UnitOfMeasurement' => 'LBS'
                    ],
                    'OverweightIndicator' => $this->chargableWeight > 69 ? 'Y' : 'N',
                    'PaymentMethod' => '01',
                    'ShippingLabelsAvailable' => 'Y',
                    'Notification' => [
                        'ConfirmationEmailAddress' => $order->user->email,
                        'UndeliverableEmailAddress' => $order->user->email,
                    ]
            ] 
        ];

        return $request_body;
    }

    private function calculateVolumetricWeight($order)
    {
        if ( $order->measurement_unit == 'kg/cm' ){
            $this->length = UnitsConverter::cmToIn($order->length);
            $this->width = UnitsConverter::cmToIn($order->width);
            $this->height = UnitsConverter::cmToIn($order->height);
            $this->weight = UnitsConverter::kgToPound($order->weight);

            $volumetricWeight = WeightCalculator::getUPSVolumnWeight($this->length,$this->width,$this->height,'in');
            return $this->chargableWeight = round($volumetricWeight >  $this->weight ? $volumetricWeight :  $this->weight,2);

        }else{

            $volumetricWeight = WeightCalculator::getUPSVolumnWeight($order->length,$order->width,$order->height,'in');
           return $this->chargableWeight = round($volumetricWeight >  $order->weight ? $volumetricWeight :  $order->weight,2);
        }
    }

    private function orderDescription($items)
    {
        foreach($items as $item)
        {
            array_push($this->itemDescription, $item->description);
        }
        $description = implode(' ', $this->itemDescription);
        
        return $description;
    }

    private function ups_ApiCall($url, $data)
    {
        try {
            $response = Http::withHeaders($this->setHeaders())->acceptJson()->post($url, $data);
           
            if($response->successful())
            {
                return (Object)[
                    'success' => true,
                    'data' => $response->json(),
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
       } catch (Exception $e) {
           
            return (object) [
                'success' => false,
                'error' => [
                    'response' => [
                        'errors' => [
                            [
                                'code' => 501,
                                'message' => $e->getMessage(),
                            ]
                        ]
                    ]
                ],
            ];
       }
    }

    private function upsApiCallForPickup($url, $data)
    {
        try {
            $response = Http::withHeaders($this->setHeaders())->acceptJson()->post($url, $data);
            
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
       } catch (Exception $e) {
            return (object) [
                'success' => false,
                'error' => [
                    'response' => [
                        'errors' => [
                            [
                                'code' => 501,
                                'message' => $e->getMessage(),
                            ]
                        ]
                    ]
                ],
            ];
       }
    }

    private function cancelUPSPickup($prn)
    {
       try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'AccessLicenseNumber' => $this->AccessLicenseNumber,
                'Password' => $this->password,
                'Username' => $this->userName,
                'Prn' => $prn,
            ])->acceptJson()->delete($this->pickup_cancel_url);

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

       } catch (Exception $e) {
            return (object) [
                'success' => false,
                'error' => [
                    'response' => [
                        'errors' => [
                            [
                                'code' => 501,
                                'message' => $e->getMessage(),
                            ]
                        ]
                    ]
                ],
            ];
       }
    }

    private function trackUPSOrder($trackingNumber)
    {
        try {
                $response = Http::withHeaders($this->setHeaders())
                        ->acceptJson()->get($this->tracking_url.$trackingNumber);
                
                if($response->successful())
                {
                    return (Object)[
                        'success' => true,
                        'data' => $response->json(),
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
            } catch (Exception $e) {
            return (object) [
                'success' => false,
                'error' => [
                    'response' => [
                        'errors' => [
                            [
                                'code' => 501,
                                'message' => $e->getMessage(),
                            ]
                        ]
                    ]
                ],
            ];
        }
    }

    private function getPaymentDetails()
    {
        return [
            'ShipmentCharge' => [
                'Type' => '01',
                'BillShipper' => [
                    'AccountNumber' => $this->shipperNumber
                ]
            ]
        ];
    }

    private function getShipmentTotalWeight()
    {
        return [
            'UnitOfMeasurement' => [
                'Code' => 'LBS',
                'Description' => 'Pounds'
            ],
            'Weight' => $this->chargableWeight
        ];
    }

    private function setHeaders()
    {
       return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'AccessLicenseNumber' => $this->AccessLicenseNumber,
            'Password' => $this->password,
            'Username' => $this->userName,
            'transId' => $this->transactionSrc,
            'transactionSrc' => 'HERCO SUITE#100',
        ];
    }
}