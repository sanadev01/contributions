<?php

namespace App\Services\Colombia;

use App\Models\Region;
use Exception;
use Illuminate\Support\Facades\Http;
use App\Services\Converters\UnitsConverter;
use App\Services\Calculators\WeightCalculator;

class ColombiaService
{
    private $userName;
    private $password;
    private $contractCode;
    private $headquarterCode;
    private $shippingUrl;

    protected $chargableWeight;

    public function __construct($userName, $password, $contractCode, $headquarterCode, $shippingUrl)
    {
        $this->userName = $userName;
        $this->password = $password;
        $this->contractCode = $contractCode;
        $this->headquarterCode = $headquarterCode;
        $this->shippingUrl = $shippingUrl;
    }

    public function getServiceRates($order, $service = 44162)
    {
        return $this->colombiaApiCall($this->shippingUrl, $this->makeRequestBody($order, true));
    }

    private function colombiaApiCall($url, $data)
    {
        try {
            
            $response = Http::withBasicAuth($this->userName, $this->password)
                                ->post($url, $data);
            
            if ($response->status() == 200) {
                
                $responseJson = $response->json()[0];
                
                if ($responseJson['intCodeError'] == 0 && $responseJson['strError'] == null) {
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
                return (Array)[
                    'success' => false,
                    'data' => null,
                    'error' => 'Server error occurred',
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
                    'boolLading' => false,
                    'placeReceiverBe' => $this->setPlace($order->recipient->toArray()),
                    'customerReceiverBe' => $this->setCustomer($order->recipient->toArray()),
                    'placeSenderBe' => $this->setPlace(null, false),
                    'customerSenderBe' => $this->setCustomer(null, false),
                    'decCollectValue' => 0,
                    'decLading' => 0,
                    'intAditionalShipping' => 0,
                    'intAditionalShipping1' => 0,
                    'intAditionalShipping2' => 0,
                    'intDeclaredValue' => 100,
                    'intHeight' => ($order->measurement_unit != 'kg/cm') ? UnitsConverter::inToCm($order->height) : $order->height,
                    'intLength' => ($order->measurement_unit != 'kg/cm') ? UnitsConverter::inToCm($order->length) : $order->length,
                    'intWidth' => ($order->measurement_unit != 'kg/cm') ? UnitsConverter::inToCm($order->width) : $order->width,
                    'intWeight' => ($order->measurement_unit != 'kg/cm') ? UnitsConverter::kgToGrams(UnitsConverter::poundToKg($this->chargableWeight)) : UnitsConverter::kgToGrams($this->chargableWeight),
                    'strAditionalShipping' => '',
                    'strIdentification' => $order->warehouse_number,
                    'strObservation' => '',
                    'strReference' => 'Referencia',
                ]
            ],
            'boolMasterGuide' => false,
            'byteGuidePDF' => false,
            'strAditionalOS' => '',
        ];
    }

    private function calculateVolumetricWeight($measurement_unit, $weight, $length, $width, $height)
    {
        $unit = ($measurement_unit == 'kg/cm') ? 'cm' : 'in';

        $volumetricWeight = WeightCalculator::getVolumnWeight($length, $width,$height, $unit);
        return $this->chargableWeight = round($volumetricWeight >  $weight ? $volumetricWeight :  $weight, 2);

    }

    private function setPlace($data = null, $typeRecipient = true)
    {
        $regionId = ($data) ? $data['region'] : '22';
        $regionCode = Region::find($regionId)->code;
        
        return [
            'intAditional' => 0,
            'intCodeCity' => $regionCode,
            'intCodeHeadquarter' => 0,
            'intCodeOperationalCenter' => 0,
            'intTypePlace' => 2,
            'strAddress' => ($data) ? $data['address'] : (($typeRecipient ? 'Colombia Receiver' : 'Colombia Sender')),
            'strAditional' => '',
            'strEmail' => '',
            'strLocker' => '',
            'strNameCountry' => 'CO',
            'strPhone' => ($data) ? $data['phone'] : '656565665',
        ];
    }

    private function setCustomer($data = null, $typeRecipient = true)
    {
        $regionId = ($data) ? $data['region'] : '22';
        $regionCode = Region::find($regionId)->code;

        return [
            'intAditional' => 0,
            'intCodeCity' => $regionCode,
            'intTypeActor' => ($typeRecipient) ? 3 : 2,
            'intTypeDocument' => 1,
            'strAddress' => ($data) ? $data['address'] : (($typeRecipient ? 'Colombia Receiver' : 'Colombia Sender')),
            'strAditional' => '',
            'strCountry' => 'CO',
            'strDocument' => '',
            'strEmail' => '',
            'strLastNames' => ($data) ? $data['last_name'] : 'Fertias',
            'strNames' => ($data) ? $data['first_name'] : 'Marcio',
            'strPhone' => ($data) ? $data['phone'] : '656565665',
        ];
    }
    
}
