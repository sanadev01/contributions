<?php
namespace App\Services\USPS;

use Exception;
use App\Models\Country;
use App\Models\ShippingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Services\Calculators\WeightCalculator;
use App\Services\USPS\ConsolidatedOrderService;

class UspsService
{
    protected $createLabelUrl;
    protected $deleteLabelUrl;
    protected $createManifestUrl;
    protected $email;
    protected $password;
    protected $getPriceUrl;
    protected $chargableWeight;
    protected $addressValidationUrl;

    public function __construct($createLabelUrl, $deleteLabelUrl, $createManifestUrl, $getPriceUrl, $addressValidationUrl, $email, $password)
    {
        $this->createLabelUrl = $createLabelUrl;
        $this->deleteLabelUrl = $deleteLabelUrl;
        $this->createManifestUrl = $createManifestUrl;
        $this->email = $email;
        $this->password = $password;
        $this->getPriceUrl = $getPriceUrl;
        $this->addressValidationUrl = $addressValidationUrl;
    }

    public function validateAddress($request)
    {
        return $this->apiCallForAddressValidation($this->getAddressValidationData($request));
    }

    private function getAddressValidationData($request)
    {
        return [
            'company_name' => 'Herco',
            'line1' => $request->address,
            'state_province' => $request->state,
            'city' => $request->city,
            'postal_code' => '',
            'country_code' => 'US'
        ];
    }

    private function apiCallForAddressValidation($data)
    {
        try {
            $response = Http::withBasicAuth($this->email, $this->password)->post($this->addressValidationUrl, $data);
            
            if($response->status() == 200) {
                
                return (Array)[
                    'success' => true,
                    'zipcode'    => $response->json()['zip5'],
                ];
            }

            if($response->status() != 200) {
                return (Array)[
                    'success' => false,
                    'message' => $response->json()['message'],
                ];
            }
            
        } catch (Exception $ex) {
            Log::info('USPS Error'. $ex->getMessage());
            return (Array)[
                'success' => false,
                'message' => $ex->getMessage(),
            ];
        }
        
    }

    public function getPrimaryLabelForRecipient($order)
    {
        if ($order->shippingService->service_sub_class == ShippingService::USPS_PRIORITY_INTERNATIONAL || $order->shippingService->service_sub_class == ShippingService::USPS_FIRSTCLASS_INTERNATIONAL) {
            return $this->uspsApiCall($this->makeRequestAttributeForInternationalLabel($order));
        }
        
        return $this->uspsApiCall($this->makeRequestAttributeForLabel($order));
    }
    
    private function makeRequestAttributeForLabel($order)
    {
        $this->calculateVolumetricWeight($order);

       return [
            'request_id' => 'HD-'.$order->id,
            'from_address' => $this->getHercoAddress(),
            'to_address' => $this->getRecipientAddress($order),
            'weight' => (float)$this->chargableWeight,
            'weight_unit' => ($order->measurement_unit == 'kg/cm') ? 'kg' : 'lb',
            'value' => (float)$order->order_value,
            'image_format' => 'pdf',
            'image_resolution' => 300,
            'usps' => [
                'shape' => 'Parcel',
                'mail_class' => $this->setServiceClass($order->shippingService->service_sub_class),
                'image_size' => '4x6',
            ],
        ];
    }

    private function makeRequestAttributeForInternationalLabel($order)
    {
        $this->calculateVolumetricWeight($order);

        return [
            'request_id' => 'HD-'.$order->id,
            'from_address' => $this->getHercoAddress(),
            'to_address' => $this->getRecipientAddress($order),
            'weight' => (float)$this->chargableWeight,
            'weight_unit' => ($order->measurement_unit == 'kg/cm') ? 'kg' : 'lb',
            'value' => (float)$order->order_value,
            'customs_form' => $this->setCustomsForm($order),
            'image_format' => 'pdf',
            'image_resolution' => 300,
            'usps' => [
                'shape' => 'Parcel',
                'mail_class' => $this->setServiceClass($order->shippingService->service_sub_class),
            ],
        ];
    }

    public function uspsApiCall($data)
    {
        try {
            
            $response = Http::withBasicAuth($this->email, $this->password)->post($this->createLabelUrl, $data);
            
            if($response->status() == 201)
            {
                return (Object)[
                    'success' => true,
                    'message' => 'Label has been generated',
                    'data'    => $response->json(),
                ];    
            }elseif($response->status() == 401)
            {
                return (Object)[
                    'success' => false,
                    'message' => $response->json()['error'],
                ];    
            }elseif ($response->status() !== 201) 
            {

                return (object) [
                    'success' => false,
                    'message' => $response->json()['message'],
                ];
            }
            
        } catch (Exception $e) {
            Log::info('USPS Error'. $e->getMessage());
            return (object) [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function deleteUSPSLabel($tracking_number)
    {
        try {
            
            $response =  Http::withBasicAuth($this->email, $this->password)->delete($this->deleteLabelUrl.$tracking_number);
            
            if($response->status() == 204)
            {
                return (Object)[
                    'success' => true,
                    'message' => 'Label has been deleted',
                ];
            }
            
            return (Object)[
                'success' => false,
                'message' => $response->json()['message'],
            ];

        } catch (Exception $e) {
            Log::info('USPS Error'. $e->getMessage());
            return (object) [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function generateManifest($container)
    {

        $data = [
            'request_id' => 'HD-'.$container->seal_no,
            'image_format' => 'pdf',
            'image_resolution' => 300,
            'usps' => [
                'tracking_numbers' => $container->orders->pluck('corrios_tracking_code')->toArray(),
            ],
        ];
        
        try {

            $response = Http::withBasicAuth($this->email, $this->password)->post($this->createManifestUrl, $data);
           
            if($response->status() == 201)
            {
                return (Object)[
                    'success' => true,
                    'message' => 'Manifest has been generated',
                    'data'    => $response->json(),
                ];    
            }elseif($response->status() == 401)
            {
                return (Object)[
                    'success' => false,
                    'message' => $response->json()['error'],
                ];    
            }elseif ($response->status() !== 201) 
            {

                return (object) [
                    'success' => false,
                    'message' => $response->json()['message'],
                ];
            }

        } catch (Exception $e) {
            Log::info('USPS Error'. $e->getMessage());
            return (object) [
                'success' => false,
                'message' => $e->getMessage(),
            ];

        }
    }

    public function getRecipientRates($order, $service)
    {
        if ($service == ShippingService::USPS_PRIORITY_INTERNATIONAL || $service == ShippingService::USPS_FIRSTCLASS_INTERNATIONAL) {
            return $this->uspsApiCallForRates($this->makeRequestAttributeForInternationalRates($order, $service));
        }
        return $this->uspsApiCallForRates($this->makeRequestAttributeForRates($order, $service));
    }

    public function makeRequestAttributeForRates($order, $service)
    {
        $this->calculateVolumetricWeight($order);

        $request_body = [
            'from_address' => $this->getHercoAddress(),
            'to_address' => $this->getRecipientAddress($order),
            'weight' => (float)$this->chargableWeight,
            'weight_unit' => ($order->measurement_unit == 'kg/cm') ? 'kg' : 'lb',
            'image_format' => 'pdf',
            'usps' => [
                'shape' => 'Parcel',
                'mail_class' => $this->setServiceClass($service),
                'image_size' => '4x6',
            ],
        ];

        if ($order->sender_country_id != Country::US) {
            $request_body['usps']['gde_origin_country_code'] = Country::find($order->sender_country_id)->code;
        }
        
        return $request_body;
    }

    private function makeRequestAttributeForInternationalRates($order, $service)
    {
        $this->calculateVolumetricWeight($order);

        return [
            'from_address' => $this->getHercoAddress(),
            'to_address' => $this->getRecipientAddress($order),
            'weight' => (float)$this->chargableWeight,
            'weight_unit' => ($order->measurement_unit == 'kg/cm') ? 'kg' : 'lb',
            'value' => (float)$order->items()->sum(DB::raw('quantity * value')),
            'customs_form' => $this->setCustomsForm($order),
            'image_format' => 'pdf',
            'usps' => [
                'shape' => 'Parcel',
                'mail_class' => $this->setServiceClass($service),
                'image_size' => '4x6',
                'gde_origin_country_code' => 'CH',
            ],
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

    public function getSenderPrice($order, $request)
    {
        if ($request->exists('consolidated_order') && $request->consolidated_order == false) {

            $consolidatedOrderService = new ConsolidatedOrderService();
            return $this->uspsApiCallForRates($consolidatedOrderService->makeConsolidatedOrderRequestForSender($order, $request));
        }

        return $this->uspsApiCallForRates($this->makeRequestForSender($order, $request));
    }

    public function getLabelForSender($order, $request)
    {
        if ($request->exists('consolidated_order') && $request->consolidated_order == false)
        {
            $consolidatedOrderService = new ConsolidatedOrderService();
            return $this->uspsApiCall($consolidatedOrderService->makeConsolidatedOrderRequestForSender($order, $request));
        }

        return $this->uspsApiCall($this->makeRequestForSender($order, $request));
    }

    private function makeRequestForSender($order, $request)
    {
        $this->calculateVolumetricWeight($order); 
        return [
            'from_address' => [
                'company_name' => 'HERCO SUIT#100',
                'first_name' => ($request->first_name) ? $request->first_name : '',
                'last_name' => ($request->last_name) ? $request->last_name.' '.$request->pobox_number : '',
                'line1' => $request->sender_address,
                'city' => $request->sender_city,
                'state_province' => $request->sender_state,
                'postal_code' => $request->sender_zipcode,
                'phone_number' => '+13058885191',
                'sms' => '+17867024093',
                'email' => 'homedelivery@homedeliverybr.com',
                'country_code' => 'US',
            ],
            'to_address' => $this->getHercoAddress(),
            'weight' => ($this->chargableWeight != null) ? (float)$this->chargableWeight : (float)$order->weight,
            'weight_unit' => ($order->measurement_unit == 'kg/cm') ? 'kg' : 'lb',
            'image_format' => 'pdf',
            'usps' => [
                'shape' => 'Parcel',
                'mail_class' => ($request->service == ShippingService::USPS_FIRSTCLASS || $request->service == 'FirstClass') ? 'FirstClass' : 'Priority',
                'image_size' => '4x6',
            ],
        ];
    }

    private function getHercoAddress()
    {
        return [
            'company_name' => 'HERCO SUITE#100',
            'line1' => '2200 NW 129TH AVE',
            'city' => 'Miami',
            'state_province' => 'FL',
            'postal_code' => '33182',
            'phone_number' => '+13058885191',
            'sms' => '+17867024093',
            'email' => 'homedelivery@homedeliverybr.com',
            'country_code' => 'US',
        ];
    }

    private function getRecipientAddress($order)
    {
        return [
            'first_name' => optional($order->recipient)->first_name,
            'last_name' => optional($order->recipient)->last_name,
            'line1' => optional($order->recipient)->address.' '.optional($order->recipient)->street_no,
            'city' => optional($order->recipient)->city,    //City validation required
            'state_province' => optional($order->recipient)->state->code,
            'postal_code' => optional($order->recipient)->zipcode,  //Zip validation required
            'phone_number' => optional($order->recipient)->phone,
            'country_code' => optional($order->recipient)->country->code, 
        ];
    }

    private function setCustomsForm($order)
    {
        return [
            'contents_type' => 'Merchandise',
            'customs_items' => $this->setItemsDetails($order),
        ];
    }

    private function setItemsDetails($order)
    {
        $items = [];
        $singleItemWeight = $this->calulateItemWeight($order);

        if (count($order->items) >= 1) {
            foreach ($order->items as $item) {
                $itemToPush = [];
                $itemToPush = [
                    'description' => $item->description,
                    'quantity' => (int)$item->quantity,
                    'value' => (float)$item->value,
                    'weight' => $singleItemWeight,
                    'weight_unit' => ($order->measurement_unit == 'kg/cm') ? 'kg' : 'lb',
                ];
               array_push($items, $itemToPush);
            }
        }

        return $items;
    }

    private function calulateItemWeight($order)
    {
        $orderTotalWeight = ($this->chargableWeight != null) ? (float)$this->chargableWeight : (float)$order->weight;
        $itemWeight = 0;

        if (count($order->items) > 1) {
            $itemWeight = $orderTotalWeight / count($order->items);
            return $itemWeight;
        }
        return $orderTotalWeight;
    }

    private function setServiceClass($service)
    {
        switch ($service) {
            case ShippingService::USPS_PRIORITY:
                return 'Priority';
                break;
            case 'Priority':
                return 'Priority';
                break;     
            case ShippingService::USPS_FIRSTCLASS:
                return 'FirstClass';
                break;
            case 'FirstClass':
                return 'FirstClass';
                break;    
            case ShippingService::USPS_PRIORITY_INTERNATIONAL:
                return 'PriorityInternational';
                break;
            case 'PriorityInternational':
                return 'PriorityInternational';
                break;    
            case ShippingService::USPS_FIRSTCLASS_INTERNATIONAL:
                return 'FirstClassInternational';
                break;               
            default:
                return 'FirstClassInternational';
                break;
        }
    }

    private function uspsApiCallForRates($data)
    {
        try {

            $response = Http::acceptJson()->withBasicAuth($this->email, $this->password)->post($this->getPriceUrl, $data);
            
            if($response->successful())
            {
                return (Object)[
                    'success' => true,
                    'data' => $response->json(),
                ];
            }elseif($response->clientError())
            {
                Log::info('USPS Error'.$response->json()['message']);
                return (Object)[
                    'success' => false,
                    'message' => $response->json()['message'],
                ];    
            }elseif ($response->status() !== 200) 
            {
                Log::info('USPS Error'.$response->json()['message']);
                return (object) [
                    'success' => false,
                    'message' => $response->json()['message'],
                ];
            }
    
        } catch (Exception $e) {
            Log::info('USPS Error'. $e->getMessage());
            return (object) [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}