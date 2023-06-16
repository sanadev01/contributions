<?php

namespace App\Services\HDExpress;

use App\Services\Converters\UnitsConverter;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Order;

class HDExpressService
{
    private $clientId;
    private $clientSecret;
    private $userName;
    private $password;
    private $getTokenUrl;
    private $houseUrl;
    private $trackingUrl;
    private $createConsolidatorUrl;
    private $registerConsolidatorUrl;
    private $createMasterUrl;
    private $registerMasterUrl;

    const MILE_EXPRESS_BRAZIL_COUNTRY_CODE = 105;
    const MILE_EXPRESS_US_COUNTRY_CODE = 249;

    public function __construct($clientId, $clientSecret, $userName, $password, $getTokenUrl, $houseUrl, $trackingUrl, $createConsolidatorUrl, $registerConsolidatorUrl, $createMasterUrl, $registerMasterUrl)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->userName = $userName;
        $this->password = $password;

        $this->getTokenUrl = $getTokenUrl;
        $this->houseUrl = $houseUrl;
        $this->trackingUrl = $trackingUrl;
        $this->createConsolidatorUrl = $createConsolidatorUrl;
        $this->registerConsolidatorUrl = $registerConsolidatorUrl;
        $this->createMasterUrl = $createMasterUrl;
        $this->registerMasterUrl = $registerMasterUrl;
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

    public function getLabel($id)
    {
        try {
            $client = new \GuzzleHttp\Client($this->setClientOptions());
            $response = $client->request('POST', $this->houseUrl.'/label', [
                'json' => [
                    'id' => $id,
                ]
            ]);

            if ($response->getStatusCode() == 200) {
                return (Object)[
                    'success' => true,
                    'data' => $response->getBody()->getContents()
                ];
            }else{
                return (Object)[
                    'success' => false,
                    'data' => null
                ];
            }

        } catch (\Exception $ex) {
            return (Object)[
                'success' => false,
                'data' => null,
                'error' => $ex->getMessage()
            ];
        }
    }

    public function createShipment($order)
    {
        return $this->mileExpressApiCall($this->houseUrl, $this->makeRequestBodyForShipment($order));
    }

    public function createContainer($request)
    {
        return $this->mileExpressApiCall($this->createConsolidatorUrl, $this->makeRequestBodyForContainer($request));
    }

    public function registerContainer($consolidatorId, $airWayBillIds)
    {
        return $this->mileExpressApiCall($this->registerConsolidatorUrl, $this->makeRequestBodyForContainerRegistration($consolidatorId, $airWayBillIds));
    }

    public function createDeilveryBill($deliverybill_Id, $destination)
    {
        return $this->mileExpressApiCall($this->createMasterUrl, $this->makeRequestBodyForDeliveryBill($deliverybill_Id, $destination));
    }

    public function registerDeliveryBill($masterId, $deliveryBillContainers)
    {
        $url = $this->registerMasterUrl.'/'.$masterId.'/attach';
        return $this->mileExpressApiCall($url, [
            'consolidators' => $deliveryBillContainers
        ]);
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
                    'height' => ($order->measurement_unit == 'kg/cm') ? $order->height : UnitsConverter::inToCm($order->height),
                    'length' => ($order->measurement_unit == 'kg/cm') ? $order->length : UnitsConverter::inToCm($order->length),
                    'width' => ($order->measurement_unit == 'kg/cm') ? $order->width : UnitsConverter::inToCm($order->width),
                    'weight' => ($order->measurement_unit == 'kg/cm') ? $order->weight : UnitsConverter::poundToKg($order->weight),
                    'items' => $this->setItems($order->items)
                ]
            ],
            'external_identifiers' => [
                $order->warehouse_number
            ],
            'description' => ($order->items->isNotEmpty()) ? $this->setOrderDescription($order->items) : 'goods',
            'amount_freight' => $order->user_declared_freight,
            'gross_weight' => ($order->measurement_unit == 'kg/cm') ? $order->weight : UnitsConverter::poundToKg($order->weight),
            'importer' => [
                'name' => $order->recipient->first_name.' '.$order->recipient->last_name,
                'addresses' => [
                    [
                        'number' => $order->recipient->street_no,
                        'additional_info' => $order->recipient->street_no,
                        'country_code' => self::MILE_EXPRESS_BRAZIL_COUNTRY_CODE,
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
                'name' => $this->setOrderSenderName($order),
                'addresses' => [
                    [
                        'number' => 100,
                        'additional_info' => 'SUITE# 100',
                        'country_code' => self::MILE_EXPRESS_US_COUNTRY_CODE,
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
                'tax_class_code' => $this->setTaxClass($item->sh_code),
                'customs_adm' => null
            ];

            array_push($itemsArr, $itemToPush);
        }

        return $itemsArr;
    }

    private function setTaxClass($shCode)
    {
        if ($shCode == '490199') {
            return '12';
        }

        if ($shCode == '293629' || $shCode == '210610') {
            return '11';
        }

        return '7';
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

    /**
     * @param Order $order
     * @return string
     */
    private function setOrderSenderName($order)
    {
        if ($order->sender_first_name) {
            return $order->sender_first_name.' '.$order->sender_last_name;
        }
        
        return 'Herco inc';
    }

    private function makeRequestBodyForContainer($request)
    {
        return [
            'origin' => 'MIA',
            'destination' => ($request->destination_operator_name == 'SAOD') ? 'GRU' : 'CWB',
            'date' => Carbon::now()->format('Y-m-d'),
        ];
    }

    private function makeRequestBodyForContainerRegistration($consolidatorId, $airWayBillIds)
    {
        return [
            'consolidator_id' => $consolidatorId,
            'airwaybill_id' => $airWayBillIds
        ];
    }

    private function makeRequestBodyForDeliveryBill($deliverybill_Id, $destination)
    {
        return [
            'code' => rand(1000, 9999).$deliverybill_Id,
            'airport_origin' => 'MIA',
            'airport_destination' => ($destination == 'SAOD') ? 'GRU' : 'CWB',
        ];
    }
    
    private function setHeaders()
    {
        return [
            'Authorization' => 'Bearer ' . $this->getToken(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    private function setClientOptions()
    {
        return [
            'base_uri' => (app()->isProduction()) ? 
                config('mileExpress.production.baseUrl') 
                : config('mileExpress.testing.baseUrl'),
            'headers' => $this->setHeaders(),
        ];
    }
}