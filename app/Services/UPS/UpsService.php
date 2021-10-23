<?php
namespace App\Services\UPS;

use Exception;
use App\Models\ShippingService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Services\Calculators\WeightCalculator;

class UpsService
{
    protected $create_package_url;
    protected $delete_package_url;
    protected $create_manifest_url;
    protected $ground_rates_url;
    protected $userName;
    protected $password;
    protected $transactionSrc;
    protected $chargableWeight;
    protected $shipperNumber;

    public function __construct($create_package_url, $delete_package_url, $create_manifest_url, $ground_rates_url, $transactionSrc, $userName, $password, $shipperNumber)
    {
        $this->create_package_url = $create_package_url;
        $this->delete_usps_label_url = $delete_package_url;
        $this->create_manifest_url = $create_manifest_url;
        $this->ground_rates_url = $ground_rates_url;
        $this->userName = $userName;
        $this->password = $password;
        $this->transactionSrc = $transactionSrc;
        $this->shipperNumber = $shipperNumber;
    }

    public function getSenderPrice($order, $request_data)
    {
       $data = $this->make_rates_request_for_sender($order, $request_data);

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'AccessLicenseNumber' => '5DA71F61D4F245F6',
                'Password' => $this->password,
                'Username' => $this->userName,
                'transId' => $this->transactionSrc,
                'transactionSrc' => 'HERCO',
            ])->acceptJson()->post($this->ground_rates_url, $data);
            
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
                    'message' => $response->json()['error'],
                ];    
            }elseif ($response->status() !== 200) 
            {

                return (object) [
                    'success' => false,
                    'message' => $response->json()['message'],
                ];
            }
       } catch (Exception $e) {
           
            return (object) [
                'success' => false,
                'message' => $e->getMessage(),
            ];
       }
    }

    public function buyLabel()
    {
        $data = $this->make_package_request_for_sender();
        // dd($data);
        $ups_response = $this->ups_ApiCall($data);
        
        return $ups_response;
    }

    private function make_rates_request_for_sender($order, $request)
    {   
        $this->calculateVolumetricWeight($order);

        $request_body = [
            'FreightRateRequest' => [
                'ShipFrom' => [
                    'Name' => ($request->first_name) ? $request->first_name : 'HERCO SUIT#100',
                    'Address' => [
                        'AddressLine' => $request->sender_address,
                        'City' => $request->sender_city,
                        'StateProvinceCode' => $request->sender_state,
                        'PostalCode' => $request->sender_zipcode,
                        'CountryCode' => 'US',
                    ],
                    'AttentionName' => ($request->first_name) ? $request->first_name : 'HERCO SUIT#100',
                    'Phone' => [
                        'Number' => '+13058885191',
                        // 'Extension' => '4444',
                    ],
                    'EMailAddress' => 'homedelivery@homedeliverybr.com'
                ],
                'ShipperNumber' => $this->shipperNumber,
                'ShipTo' => [
                    'Name' => 'HERCO SUIT#100',
                    'Address' => [
                        'AddressLine' => '2200 NW 129TH AVE',
                        'City' => 'Miami',
                        'StateProvinceCode' => 'FL',
                        'PostalCode' => '33182',
                        'CountryCode' => 'US',
                    ],
                    'AttentionName' => 'Marcio',
                    'Phone' => [
                        'Number' => '+13058885191',
                        // 'Extension' => '4444',
                    ],
                    'EMailAddress' => 'homedelivery@homedeliverybr.com'
                ],
                'PaymentInformation' => [
                    'Payer' => [
                        'Name' => 'HERCO SUIT#100',
                        'Address' => [
                            'AddressLine' => '2200 NW 129TH AVE',
                            'City' => 'Miami',
                            'StateProvinceCode' => 'FL',
                            'PostalCode' => '33182',
                            'CountryCode' => 'US',
                        ],
                        'ShipperNumber' => 'AT0123',
                        'AccountType' => '1',
                        'AttentionName' => 'Marcio',
                        'Phone' => [
                            'Number' => '+13058885191',
                            // 'Extension' => '4444',
                        ],
                        'EMailAddress' => 'homedelivery@homedeliverybr.com'
                    ],
                    'ShipmentBillingOption' => [
                        'Code' => '10',
                    ],
                ],
                'Service' =>  [
                    'Code' => $request->service,
                ],
                'Commodity' => [
                    'Description' => 'FRS-Freight',
                    'Weight' => [
                        'UnitOfMeasurement' => [
                            'Code' => ($order->measurement_unit == 'kg/cm') ? 'KGS' : 'LBS',
                        ],
                        'Value' => ($this->chargableWeight != null) ? "$this->chargableWeight" : "$order->weight",
                    ],
                    'Dimensions' => [
                        'UnitOfMeasurement' => [
                            'Code' => ($order->measurement_unit == 'kg/cm') ? 'CM' : 'IN',
                            'Description' => ''
                        ],
                        'Length' => $order->length,
                        'Width' => $order->width,
                        'Height' => $order->height,
                    ],
                    'NumberOfPieces' => '1',
                    'PackagingType' => [
                        'Code' => 'BOX',
                    ],
                    'FreightClass' => '60',

                ],
                'DensityEligibleIndicator' => '',
                // 'AlternateRateOptions' => [
                //     'Code' => '1',
                // ],
            ],
        ];

        return $request_body;
    }

    private function make_package_request_for_sender()
    {
        // $this->calculateVolumetricWeight($order);

        $request_body = [
            'FreightShipRequest' => [
                'Shipment' => [
                    'ShipFrom' => [
                        'Name' => 'Ghazi Khan',
                        'Address' => [
                            'AddressLine' => '123 Lane',
                            'City' => 'TIMONIUM',
                            'StateProvinceCode' => 'MD',
                            'PostalCode' => '21093',
                            'CountryCode' => 'US',
                        ],
                        'AttentionName' => 'Ghazi',
                        'Phone' => [
                            'Number' => '+13058885191',
                        ],
                        'EMailAddress' => 'ghaziislam3@gmail.com'
                    ],
                    'ShipperNumber' => $this->shipperNumber,
                    'ShipTo' => [
                        'Name' => 'HERCO SUIT#100',
                        'Address' => [
                            'AddressLine' => '2200 NW 129TH AVE',
                            'City' => 'Miami',
                            'StateProvinceCode' => 'FL',
                            'PostalCode' => '33182',
                            'CountryCode' => 'US',
                        ],
                        'AttentionName' => 'Marcio',
                        'Phone' => [
                            'Number' => '+13058885191',
                        ],
                        'EMailAddress' => 'homedelivery@homedeliverybr.com'
                    ],
                    'PaymentInformation' => [
                        'Payer' => [
                            'Name' => 'HERCO SUIT#100',
                            'Address' => [
                                'AddressLine' => '2200 NW 129TH AVE',
                                'City' => 'Miami',
                                'StateProvinceCode' => 'FL',
                                'PostalCode' => '33182',
                                'CountryCode' => 'US',
                            ],
                            'ShipperNumber' => 'AT0123',
                            'AccountType' => '1',
                            'AttentionName' => 'Marcio',
                            'Phone' => [
                                'Number' => '+13058885191',
                                // 'Extension' => '4444',
                            ],
                            'EMailAddress' => 'homedelivery@homedeliverybr.com'
                        ],
                        'ShipmentBillingOption' => [
                            'Code' => '10',
                        ],
                    ],
                    'Documents' => [
                        'Image' => [
                            'Type' => [
                                'Code' => '30'
                            ],
                            'LabelsPerPage' => '1',
                            'Format' => [
                                'Code' => '01',
                            ],
                            'PrintFormat' => [
                                'Code' => '02',
                            ],
                            'PrintSize' => [
                                'Length' => '4',
                                'Width' => '6'
                            ]

                        ]
                    ],
                    'Service' =>  [
                        'Code' => '308',
                    ],
                    'HandlingUnitOne' => [
                        'Quantity' => '2',
                        'Type' => [
                            'Code' => 'PLT',
                        ]
                    ],
                    'Commodity' => [
                        'Description' => 'Goods',
                        'Weight' => [
                            'UnitOfMeasurement' => [
                                'Code' => 'LBS',
                            ],
                            'Value' => "30",
                        ],
                        'Dimensions' => [
                            'UnitOfMeasurement' => [
                                'Code' => 'IN',
                                'Description' => ''
                            ],
                            'Length' => '4',
                            'Width' => '5',
                            'Height' => '6',
                        ],
                        'NumberOfPieces' => '1',
                        'PackagingType' => [
                            'Code' => 'BOX',
                        ],
                        'FreightClass' => '60',
    
                    ],
                    'DensityEligibleIndicator' => '',
                    'PickupRequest' => [
                        'Requester' => [
                            'AttentionName' => 'Marcio',
                            'EMailAddress' => 'marcio@gmail.com',
                            'Name' => 'Marcio',
                            'Phone' => [
                                'Number' => '+13058885191'
                            ]
                        ],
                        'PickupDate' => '20211027',
                        'EarliestTimeReady' => '0723',
                        'LatestTimeReady' => '1700',
                    ],
                    'TimeInTransitIndicator' => '',
                ],
                'Miscellaneous' => [
                    'WSVersion' => '21.0.11',
                    'ReleaseID' => '07.12.2008'
                ]
            ],
        ];

        return $request_body;
    }

    private function calculateVolumetricWeight($order)
    {
        if ( $order->measurement_unit == 'kg/cm' ){

            $volumetricWeight = WeightCalculator::getVolumnWeight($order->length,$order->width,$order->height,'cm');
            return $this->chargableWeight = round($volumetricWeight >  $order->weight ? $volumetricWeight :  $order->weight,2);

        }else{

            $volumetricWeight = WeightCalculator::getVolumnWeight($order->length,$order->width,$order->height,'in');
           return $this->chargableWeight = round($volumetricWeight >  $order->weight ? $volumetricWeight :  $order->weight,2);
        }
    }

    private function ups_ApiCall($data)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'AccessLicenseNumber' => '5DA71F61D4F245F6',
                'Password' => $this->password,
                'Username' => $this->userName,
                'transId' => $this->transactionSrc,
                'transactionSrc' => 'HERCO',
            ])->acceptJson()->post($this->create_package_url, $data);
            
            dd($response->json());
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
                    'message' => $response->json()['error'],
                ];    
            }elseif ($response->status() !== 200) 
            {

                return (object) [
                    'success' => false,
                    'message' => $response->json()['message'],
                ];
            }
       } catch (Exception $e) {
           
            return (object) [
                'success' => false,
                'message' => $e->getMessage(),
            ];
       }
    }
}