<?php

namespace App\Services\Colombia;

use Exception;
use App\Models\Region;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use App\Services\Converters\UnitsConverter;
use App\Services\Calculators\WeightCalculator;

class ColombiaService
{
    private $userName;
    private $password;
    private $contractCode;
    private $headquarterCode;
    private $token;
    private $shippingUrl;
    private $containerRegisterUrl;

    protected $chargableWeight;

    public function __construct($userName=null, $password=null, $contractCode=null, $headquarterCode=null, $token=null, $shippingUrl=null, $containerRegisterUrl=null)
    {
        $this->userName= config('ColombiaService.credentials.username');
        $this->password = config('ColombiaService.credentials.password');
        $this->contractCode = config('ColombiaService.credentials.contractCode');
        $this->headquarterCode = config('ColombiaService.credentials.headquarterCode');
        $this->token = config('ColombiaService.credentials.token');
        if(app()->isProduction()){
            $this->shippingUrl = config('ColombiaService.production.shippingUrl');
            $this->containerRegisterUrl = config('ColombiaService.production.containerRegisterUrl');
        }else{ 
            $this->shippingUrl = config('ColombiaService.testing.shippingUrl');
            $this->containerRegisterUrl = config('ColombiaService.testing.containerRegisterUrl');
        }
       
    }

    public function getServiceRates($order, $service = 44162)
    {
        return $this->colombiaApiCall($this->shippingUrl, $this->makeRequestBody($order, true));
    }

    public function createShipment($order)
    {
        return $this->colombiaApiCall($this->shippingUrl, $this->makeRequestBody($order, false));
    }

    public function registerContainer($container)
    {
        return $this->colombiaApiCallWithToken($this->containerRegisterUrl, $this->makeContainerRequestBody($container));
    }

    private function makeContainerRequestBody($container)
    {
        return [
            'cargaPruebasEntrega' => [
                'nitEmpresa' => '900062917-9',
                'listaGuias' => [
                    'numerosGuia' => $this->setTrackingCodes($container),
                ],
            ]
        ];
    }

    private function setTrackingCodes($container)
    {
        $orderTrackingCodes = [];

        foreach ($container->orders as $order) {
            $orderTrackingCodes[] = $order->corrios_tracking_code;
        }

        return $orderTrackingCodes;
    }

    private function colombiaApiCallWithToken($url, $data)
    {
        try {
            
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' .$this->token,
            ])->post($url, $data);

            if ($response->status() == 200) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'error' => null,
                ];
            }

            return [
                'success' => false,
                'data' => null,
                'error' => 'Error while calling Colombia API',
            ];

        } catch (Exception $ex) {
            
            return [
                'success' => false,
                'data' => null,
                'error' => $ex->getMessage(),
            ];
        }
    }
    
    private function colombiaApiCall($url, $data)
    {
        try {
            $response = Http::withBasicAuth($this->userName, $this->password)->post($url, $data);     
            if ($response->status() == 200) {
                $responseJson = collect($response->json())->first();
                if ($responseJson['intCodeError'] == 0  && $responseJson['strUrlGuide'] != null) {
                    return (Array)[
                        'success' => true,
                        'data' => $responseJson,
                        'error' => null
                    ];
                }
                else{
                    return (Array)[
                        'success' => false,
                        'data' => null,
                        'error' => $responseJson['strError'],
                    ];
                }
            }
            else{
                $errorResponse = $response->json();
                return (Array)[
                    'success' => false,
                    'data' => null,
                    'error' => $errorResponse[0]['strError'],
                ];
            }
            
        } catch (Exception $ex) {
            return (Array)[
                'success' => false,
                'data' => null,
                'error' => $ex->getMessage(),
            ];
        }
    }

    private function makeRequestBody($order, $forRates = false)
    {
        $this->calculateVolumetricWeight($order->measurement_unit, $order->weight, $order->length, $order->width, $order->height);

        return [
            'intAditionalOS' => 0,
            'intCodeContract' => (int)$this->contractCode,
            'intCodeHeadquarter' => (int)$this->headquarterCode,
            'intCodeService' => 44,
            'intGuidesNumber' => 1,
            'intTypePay' => 3,
            'intTypeRequest' => ($forRates) ? 1 : 2,
            'lstShippingTraceBe' => [
                [
                    'placeReceiverBe' => $this->setReceiverPlace($order->recipient->toArray()),
                    'boolLading' => false,
                    'customerReceiverBe' => $this->setReceiver($order->recipient->toArray()),
                    'customerSenderBe' => $this->setSender($order),
                    'decCollectValue' => 0,
                    'decLading' => 0,
                    'intAditionalShipping' => 0,
                    'intAditionalShipping1' => 0,
                    'intAditionalShipping2' => 0,
                    'intDeclaredValue' => ($order->order_value > 0) ? round(($order->order_value * 3976.49)) : 100,
                    'intHeight' => ($order->measurement_unit != 'kg/cm') ? round(UnitsConverter::inToCm($order->height)) : round($order->height),
                    'intLength' => ($order->measurement_unit != 'kg/cm') ? round(UnitsConverter::inToCm($order->length)) : round($order->length),
                    'intWidth' => ($order->measurement_unit != 'kg/cm') ? round(UnitsConverter::inToCm($order->width)) : round($order->width),
                    'intWeight' => ($order->measurement_unit != 'kg/cm') ? round(UnitsConverter::kgToGrams(UnitsConverter::poundToKg($this->chargableWeight))) : round(UnitsConverter::kgToGrams($this->chargableWeight)),
                    'placeSenderBe' => $this->setSenderPlace($order),
                    'strAditionalShipping' => '',
                    'strIdentification' => $order->warehouse_number,
                    'strObservation' => '',
                    'strReference' => ($order->customer_reference) ? $order->customer_reference.' '.$order->user->pobox_number : $order->user->pobox_number,
                ]
            ],
            'boolMasterGuide' => false,
            'strAditionalOS' => '',
        ];
    }

    private function calculateVolumetricWeight($measurement_unit, $weight, $length, $width, $height)
    {
        $unit = ($measurement_unit == 'kg/cm') ? 'cm' : 'in';

        $volumetricWeight = WeightCalculator::getVolumnWeight($length, $width,$height, $unit);
        return $this->chargableWeight = round($volumetricWeight >  $weight ? $volumetricWeight :  $weight, 2);

    }

    private function setReceiverPlace($data = null, $typeRecipient = true)
    {
        $regionId = ($data) ? $data['region'] : null;
        if(!empty($data)) {
            $regionCode = $data['zipcode'];
        } else {
            $regionCode = ($regionId) ? Region::find($regionId)->code : Region::COLOMBIA_SENDER_CODE;
        }
        
        return [
            'intAditional' => 0,
            'intCodeCity' => $regionCode,
            'intCodeHeadquarter' => 0,
            'intCodeOperationalCenter' => 0,
            'intTypePlace' => 2,
            'strAddress' => $data['address'],
            'strAditional' => '',
            'strEmail' => ($data) ? $data['email'] : '',
            'strLocker' => '',
            'strNameCountry' => 'CO',
            'strPhone' => ($data) ? $data['phone'] : '+13058885191',
        ];
    }

    private function setReceiver($data = null, $typeRecipient = true)
    {
        $regionId = ($data) ? $data['region'] : null;
        if(!empty($data)) {
            $regionCode = $data['zipcode'];
        }else {
            $regionCode = ($regionId) ? Region::find($regionId)->code : Region::COLOMBIA_SENDER_CODE;
        }
        
        return [
            'intAditional' => 0,
            'intCodeCity' => $regionCode,
            'intTypeActor' => 3,
            'intTypeDocument' => 1,
            'strAddress' => $data['address'],
            'strAditional' => '',
            'strCountry' => 'CO',
            'strDocument' => '',
            'strEmail' => ($data) ? $data['email'] : '',
            'strLastNames' => ($data) ? $data['last_name'] : 'Fertias',
            'strNames' => ($data) ? $data['first_name'] : 'Marcio',
            'strPhone' => ($data) ? $data['phone'] : '+13058885191',
        ];
    }

    private function setSenderPlace($order)
    {
        
        return [
            'intAditional' => 0,
            'intCodeCity' => Region::COLOMBIA_SENDER_CODE,
            'intCodeHeadquarter' => 0,
            'intCodeOperationalCenter' => 0,
            'intTypePlace' => 2,
            'strAddress' => '2200 NW 129TH AVE, Miami FL, 33182',
            'strAditional' => '',
            'strEmail' => ($order->sender_email) ? $order->sender_email : '',
            'strLocker' => '',
            'strNameCountry' => 'CO',
            'strPhone' => ($order->sender_phone) ? $order->sender_phone : '+13058885191',
        ];
    }

    private function setSender($order)
    {        
        return [
            'intAditional' => 0,
            'intCodeCity' => Region::COLOMBIA_SENDER_CODE,
            'intTypeActor' => 2,
            'intTypeDocument' => 1,
            'strAddress' => '2200 NW 129TH AVE, Miami FL, 33182',
            'strAditional' => '',
            'strCountry' => 'CO',
            'strDocument' => '',
            'strEmail' => ($order->sender_email) ? $order->sender_email : '',
            'strLastNames' => ($order->sender_last_name) ? $order->sender_last_name : '',
            'strNames' => ($order->sender_first_name) ? $order->sender_first_name : '',
            'strPhone' => ($order->sender_phone) ? $order->sender_phone : '+13058885191',
        ];
    }
    
}