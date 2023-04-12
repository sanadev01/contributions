<?php
namespace App\Services\USPS;

use Exception;
use App\Models\Order;
use App\Models\State;
use App\Models\Country;
use App\Models\ShippingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Services\Calculators\WeightCalculator;

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
        return $this->uspsApiCall($this->makeRequestAttributeForLabel($order), $this->createLabelUrl);
    }
    
    private function makeRequestAttributeForLabel($order)
    {
        $this->calculateVolumetricWeight($order);

        $request_body = [
            'request_id' => 'HD-'.$order->id,
            'from_address' => ($order->sender_country_id == Order::US && $order->recipient->country_id != Order::US) ? $this->getSenderAddress($order) : $this->getHercoAddress($order->warehouse_number),
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

        if ($order->shippingService->service_sub_class == ShippingService::USPS_PRIORITY_INTERNATIONAL || $order->shippingService->service_sub_class == ShippingService::USPS_FIRSTCLASS_INTERNATIONAL) {
            $request_body = array_add($request_body, 'customs_form', $this->setCustomsForm($order));
            array_forget($request_body, 'usps.image_size');
        }

        if ($order->sender_country_id != Country::US && ($order->shippingService->service_sub_class == ShippingService::USPS_PRIORITY || $order->shippingService->service_sub_class == ShippingService::USPS_FIRSTCLASS)) {
            $request_body['usps']['gde_origin_country_code'] = Country::find($order->sender_country_id)->code;
        }
        
        return $request_body;
    }

    private function uspsApiCall($data, $url)
    {
        try {
            
            $response = Http::withBasicAuth($this->email, $this->password)->post($url, $data);
            
            if($response->status() == 201 || $response->successful())
            {
                return (Object)[
                    'success' => true,
                    'message' => ($url == $this->getPriceUrl) ? 'rates has been applied' : 'Label has been generated',
                    'data'    => $response->json(),
                ];    
            }elseif($response->status() == 401 || $response->clientError())
            {
                return (Object)[
                    'success' => false,
                    'message' => ($url == $this->getPriceUrl) ? $response->json()['message']  : $response->json()['error'],
                ];    
            }elseif ($response->status() !== 200) 
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
        return $this->uspsApiCall($this->makeRequestAttributeForRates($order, $service), $this->getPriceUrl);
    }

    private function makeRequestAttributeForRates($order, $service)
    {
        $this->calculateVolumetricWeight($order);

        $request_body = [
            'from_address' => ($order->id === 1) ? $this->getSenderAddress($order) : $this->getHercoAddress($order->warehouse_number),
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

        if ($service == ShippingService::USPS_PRIORITY_INTERNATIONAL || $service == ShippingService::USPS_FIRSTCLASS_INTERNATIONAL) {
            
            $request_body = array_add($request_body, 'value', $this->calculateItemsValue($order->items));

            $request_body = array_add($request_body, 'customs_form', $this->setCustomsForm($order));

            $request_body = array_add($request_body, 'usps.gde_origin_country_code', optional($order->recipient)->country->code);
        }

        if ($order->sender_country_id != Country::US && ($service != ShippingService::USPS_PRIORITY_INTERNATIONAL || $service != ShippingService::USPS_FIRSTCLASS_INTERNATIONAL)) {
            $request_body['usps']['gde_origin_country_code'] = Country::find($order->sender_country_id)->code;
        }
        
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

    public function getSenderRates($order, $request)
    {
        return $this->uspsApiCall($this->makeRequestForSender($order, $request), $this->getPriceUrl);
    }

    public function getLabelForSender($order, $request)
    {
        return $this->uspsApiCall($this->makeRequestForSender($order, $request), $this->createLabelUrl);
    }

    private function makeRequestForSender($order, $request)
    {
        $this->calculateVolumetricWeight($order);

        $request_body = [
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
            'to_address' => $this->getHercoAddress($order->warehouse_number),
            'weight' => ($this->chargableWeight != null) ? (float)$this->chargableWeight : (float)$order->weight,
            'weight_unit' => ($order->measurement_unit == 'kg/cm') ? 'kg' : 'lb',
            'image_format' => 'pdf',
            'image_resolution' => 300,
            'usps' => [
                'shape' => 'Parcel',
                'mail_class' => $this->setServiceClass($request->service),
                'image_size' => '4x6',
            ],
        ];

        if ($request->service == ShippingService::USPS_PRIORITY_INTERNATIONAL || $request->service == ShippingService::USPS_FIRSTCLASS_INTERNATIONAL) {
            $request_body = array_add($request_body, 'customs_form', $this->setCustomsForm($order));
            array_forget($request_body, 'usps.image_size');
        }

        return $request_body;
    }

    private function getHercoAddress($warehouse_number)
    {
        return [
            'company_name' => 'HERCO SUITE#100 -'.$warehouse_number,
            'line1' => '8305 NW 116TH AVENUE',
            'city' => 'Miami',
            'state_province' => 'FL',
            'postal_code' => '33178',
            'phone_number' => '+13058885191',
            'sms' => '+17867024093',
            'email' => 'homedelivery@homedeliverybr.com',
            'country_code' => 'US',
        ];
    }

    private function getSenderAddress($order)
    {
        return [
            'company_name' => $order->getSenderFullName().'-'.$order->warehouse_number, //sender name
            'line1' => $order->sender_address,
            'city' => $order->sender_city,
            'state_province' => ($order->sender_state) ? $order->sender_state : State::where('id', $order->sender_state_id)->value('code'),
            'postal_code' => $order->sender_zipcode,
            'phone_number' => $order->sender_phone??($order->user->phone??'+13058885191'),//sender phone
            'sms' => '+13058885191',
            'email' => $order->sender_email??($order->user->email??'homedelivery@homedeliverybr.com'),
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

    private function calculateItemsValue($orderItems)
    {
        $itemsValue = 0;
        foreach ($orderItems as $item) {
            $itemsValue += $item->value * $item->quantity;
        }
       
        return $itemsValue;
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
            foreach ($order->items as $key => $item) {
                $itemToPush = [];
                $itemToPush = [
                    'description' => $item->description,
                    'quantity' => (int)$item->quantity,
                    'value' => (float)$item->value,
                    'weight' => $singleItemWeight / (int)$item->quantity,
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
            case ShippingService::USPS_GROUND:
                return 'ParcelSelect';
                break;
            case 'ParcelSelect':
                return 'ParcelSelect';
                break;     
            case ShippingService::USPS_FIRSTCLASS_INTERNATIONAL:
                return 'FirstClassInternational';
                break;               
            default:
                return 'FirstClassInternational';
                break;
        }
    }
}