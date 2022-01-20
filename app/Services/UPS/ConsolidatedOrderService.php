<?php

namespace App\Services\UPS;

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

    public function makeConsolidatedOrderRatesRequestForSender($order, $request)
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

    public function getWeightInLbs($weight)
    {
       $weight = UnitsConverter::kgToPound($weight);
       return "$weight";
    }
}
