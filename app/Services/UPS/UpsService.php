<?php
namespace App\Services\UPS;

use Exception;
use App\Models\ShippingService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Services\Calculators\WeightCalculator;

class UpsService
{
    protected $package_url;
    protected $delete_package_url;
    protected $create_manifest_url;
    protected $ground_rates_url;
    protected $userName;
    protected $password;
    protected $transactionSrc;
    protected $chargableWeight;

    public function __construct($package_url, $delete_package_url, $create_manifest_url, $ground_rates_url, $transactionSrc, $userName, $password)
    {
        $this->package_url = $package_url;
        $this->delete_usps_label_url = $delete_package_url;
        $this->create_manifest_url = $create_manifest_url;
        $this->ground_rates_url = $ground_rates_url;
        $this->userName = $userName;
        $this->password = $password;
        $this->transactionSrc = $transactionSrc;
    }

    public function getSenderPrice($order, $request_data)
    {
        // dd($order->toArray(), $request_data);
       $data = $this->make_rates_request_for_sender($order, $request_data);
    //    dd($data);
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
                'ShipperNumber' => 'AT0123',
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
}