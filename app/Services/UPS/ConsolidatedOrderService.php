<?php

namespace App\Services\UPS;

use Illuminate\Support\Facades\Auth;
use App\Services\Converters\UnitsConverter;

class ConsolidatedOrderService
{
    protected $paymentDetails;
    protected $shipperNumber;

    public function handle($paymentDetails, $shipperNumber)
    {
        $this->paymentDetails = $paymentDetails;
        $this->shipperNumber = $shipperNumber;    
    }

    public function consolidatedOrderRatesRequestForSender($order, $request)
    {
        return [
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
                        'Name' => $order['user'] ? $order['user']['pobox_number'] :  'HERCO SUITE#100',
                        'AttentionName' => ($request->first_name) ? $request->first_name : 'HERCO SUITE#100',
                        'ShipperNumber' => $this->shipperNumber,
                        'Phone' => [
                            'Number' => $order['user'] ? $order['user']['phone'] : '+13058885191'
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
                    'PaymentDetails' => $this->paymentDetails,
                    'Service' =>  [
                        'Code' => '0'.$request->service,
                        'Description' => 'Ground Service'
                    ],
                    'ShipmentTotalWeight' => $this->getShipmentTotalWeight($order['weight']),
                    'Package' => [
                        'PackagingType' => [
                            'Code' => '02',
                            'Description' => 'Package'
                        ],
                        'Dimensions' => [
                            'UnitOfMeasurement' => [
                                'Code' => 'IN',
                            ],
                            'Length' => (String)$order['length'],
                            'Width' => (String)$order['width'],
                            'Height' => (String)$order['height'],
                        ],
                        'PackageWeight' => [
                            'UnitOfMeasurement' => [
                                'Code' => 'LBS',
                            ],
                            'Weight' =>  (String)UnitsConverter::kgToPound($order['weight']),
                        ],
                        'ShipmentServiceOptions' => [
                            'DirectDeliveryOnlyIndicator' => '1'
                        ],
                    ],
                ],
            ],
        ];
    }

    public function consolidatedOrderPickupRequest($order, $request)
    {
        return [
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
                        'Number' => $order['user']['phone']
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
                        'Weight' => (String)UnitsConverter::kgToPound($order['weight']),
                        'UnitOfMeasurement' => 'LBS'
                    ],
                    'OverweightIndicator' => (UnitsConverter::kgToPound($order['weight'])) > 69 ? 'Y' : 'N',
                    'PaymentMethod' => '01',
                    'ShippingLabelsAvailable' => 'Y',
                    'Notification' => [
                        'ConfirmationEmailAddress' => $order['user']['email'],
                        'UndeliverableEmailAddress' => $order['user']['email'],
                    ]
            ] 
        ];
    }

    public function consolidatedOrderPackageRequestForSender($order, $request)
    {
        return [
            'ShipmentRequest' => [
                'Shipment' => [
                    'Description' => 'goods',
                    'Shipper' => [
                        'Name' => $order['user'] ? $order['user']['pobox_number'] :  'HERCO SUITE#100',
                        'AttentionName' => $order['user'] ? $request->first_name.' '.$request->last_name : 'HERCO SUITE#100',
                        'ShipperNumber' => $this->shipperNumber,
                        'Phone' => [
                            'Number' => $order['user'] ? $order['user']['phone'] : '+13058885191'
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
                        'AttentionName' => $order['user'] ? $order['user']['pobox_number'] : Auth::user()->pobox_number,
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
                        'Name' => ($request->first_name) ? $request->first_name.' '.$request->last_name : 'HERCO SUITE#100',
                        'AttentionName' => $request->first_name.' '.$request->last_name,
                        'Address' => [
                            'AddressLine' => $request->sender_address,
                            'City' => $request->sender_city,
                            'StateProvinceCode' => $request->sender_state,
                            'PostalCode' => $request->sender_zipcode,
                            'CountryCode' => 'US',
                        ],
                        'Phone' => [
                            'Number' => $order['user'] ? $order['user']['phone'] : '+13058885191'
                        ],
                    ],
                    'PaymentInformation' => $this->getPaymentDetails(),
                    'Service' => [
                        'Code' => '0'.$request->service,
                        'Description' => 'Ground Service'
                    ],
                    'Package' => [
                        [
                            'Description' => 'goods',
                            'Packaging' => [
                                'Code' => '02',
                                'Description' => 'Customer Supplied Package'
                            ],
                            'Dimensions' => [
                                'UnitOfMeasurement' => [
                                    'Code' => 'IN',
                                ],
                                'Length' => (String)$order['length'],
                                'Width' => (String)$order['width'],
                                'Height' => (String)$order['height'],
                            ],
                            'PackageWeight' => [
                                'UnitOfMeasurement' => [
                                    'Code' => 'LBS',
                                ],
                                'Weight' => (String)UnitsConverter::kgToPound($order['weight']),
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
    }
    
    
    private function getShipmentTotalWeight($weight)
    {
        return [
            'UnitOfMeasurement' => [
                'Code' => 'LBS',
                'Description' => 'Pounds'
            ],
            'Weight' => UnitsConverter::kgToPound($weight),
        ];
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
}
