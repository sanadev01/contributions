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
    protected $sender_name = 'HERCO';

    public function __construct($create_package_url, $delete_package_url, $create_manifest_url, $rating_package_url, $transactionSrc, $userName, $password, $shipperNumber, $AccessLicenseNumber)
    {
        $this->create_package_url = $create_package_url;
        $this->delete_usps_label_url = $delete_package_url;
        $this->create_manifest_url = $create_manifest_url;
        $this->rating_package_url = $rating_package_url;
        $this->userName = $userName;
        $this->password = $password;
        $this->transactionSrc = $transactionSrc;
        $this->shipperNumber = $shipperNumber;
        $this->AccessLicenseNumber = $AccessLicenseNumber;
    }

    public function generateLabel($order)
    {
        $data = $this->make_package_request_for_recipient($order);
        $ups_response = $this->ups_ApiCall($data);

        return $ups_response;
    }

    public function getSenderPrice($order, $request_data)
    {   
       $this->sender_name = $request_data->first_name . ' ' . $request_data->last_name; 
       $data = $this->make_rates_request_for_sender($order, $request_data);
        
       return $this->upsApiCallForRates($data);
    }

    public function buyLabel($order, $request_sender_data)
    {
        $this->sender_name = $request_sender_data->first_name . ' ' . $request_sender_data->last_name; 
        $data = $this->make_package_request_for_sender($order, $request_sender_data);
        
        $ups_response = $this->ups_ApiCall($data);
        return $ups_response;
    }

    public function getRecipientRates($order, $service)
    {
        $data = $this->make_rates_request_for_recipient($order, $service);

        return $this->upsApiCallForRates($data);
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
                        'Name' => Auth::user() ? Auth::user()->pobox_number :  'HERCO SUIT#100',
                        'AttentionName' => ($request->first_name) ? $request->first_name : 'HERCO SUIT#100',
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
                        'Name' => ($request->first_name) ? $request->first_name : 'HERCO SUIT#100',
                        'Address' => [
                            'AddressLine' => $request->sender_address,
                            'City' => $request->sender_city,
                            'StateProvinceCode' => $request->sender_state,
                            'PostalCode' => $request->sender_zipcode,
                            'CountryCode' => 'US',
                        ],
                    ],
                    'ShipTo' => [
                        'Name' => 'HERCO SUIT#100',
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
                    'Description' => '1206 PTR',
                    'Shipper' => [
                        'Name' => Auth::user() ? Auth::user()->pobox_number :  'HERCO SUIT#100',
                        'AttentionName' => $this->sender_name,
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
                        'Name' => 'HERCO SUIT#100',
                        'AttentionName' => 'Marcio',
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
                            'Description' => 'Goods',
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
                        'Name' => Auth::user() ? Auth::user()->pobox_number :  'HERCO SUIT#100',
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
                        'Name' => 'HERCO SUIT#100',
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
                    'Description' => '1206 PTR',
                    'Shipper' => [
                        'Name' => $order->sender_first_name.' '.$order->sender_last_name,
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
                        'AttentionName' => $order->recipient->first_name.' '.$order->recipient->last_name,
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

    private function upsApiCallForRates($data)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'AccessLicenseNumber' => $this->AccessLicenseNumber,
                'Password' => $this->password,
                'Username' => $this->userName,
                'transId' => $this->transactionSrc,
                'transactionSrc' => 'HERCO',
            ])->acceptJson()->post($this->rating_package_url, $data);
            
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
                'error' => $e->getMessage(),
            ];
       }
    }

    private function ups_ApiCall($data)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'AccessLicenseNumber' => $this->AccessLicenseNumber,
                'Password' => $this->password,
                'Username' => $this->userName,
                'transId' => $this->transactionSrc,
                'transactionSrc' => 'HERCO',
            ])->acceptJson()->post($this->create_package_url, $data);
           
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
}