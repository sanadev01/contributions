<?php
namespace App\Services\USPS;

use Exception;
use App\Models\ShippingService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
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

    public function generateLabel($order)
    {
        return $this->uspsApiCall($this->makeRequestAttributeForLabel($order));
    }
    
    private function makeRequestAttributeForLabel($order)
    {
        $this->calculateVolumetricWeight($order);

        $request_body = [
            'request_id' => 'HD-'.$order->id,
            'from_address' => [
                'company_name' => 'HERCO SUITE#100',
                'line1' => '2200 NW 129TH AVE',
                'city' => 'Miami',
                'state_province' => 'FL',
                'postal_code' => '33182',
                'phone_number' => '+13058885191',
                'sms' => '+17867024093',
                'email' => 'homedelivery@homedeliverybr.com',
                'country_code' => 'US',
            ],
            'to_address' => [
                'first_name' => $order->recipient->first_name,
                'last_name' => $order->recipient->last_name,
                'line1' => $order->recipient->address.' '.$order->recipient->street_no,
                'city' => $order->recipient->city,    //City validation required
                'state_province' => $order->recipient->state->code,
                'postal_code' => $order->recipient->zipcode,  //Zip validation required
                'phone_number' => $order->recipient->phone,
                'country_code' => 'US', 
            ],
            'weight' => (float)$this->chargableWeight,
            'weight_unit' => ($order->measurement_unit == 'kg/cm') ? 'kg' : 'lb',
            'value' => (float)$order->order_value,
            'image_format' => 'pdf',
            'image_resolution' => 300,
            'usps' => [
                'shape' => 'Parcel',
                'mail_class' => $order->shipping_service_name,
                'image_size' => '4x6',
            ],
        ];

        return $request_body;
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

    public function getPrice($order, $service)
    {
       $data = $this->makeRequestAttributeForRates($order, $service);
       
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
            Log::info('USPS Error'. $e->getMessage());
            return (object) [
                'success' => false,
                'message' => $e->getMessage(),
            ];
       }
    }

    public function makeRequestAttributeForRates($order, $service)
    {
        $this->calculateVolumetricWeight($order);

        $request_body = [
            'from_address' => [
                'company_name' => 'HERCO SUITE#100',
                'line1' => '2200 NW 129TH AVE',
                'city' => 'Miami',
                'state_province' => 'FL',
                'postal_code' => '33182',
                'phone_number' => '+13058885191',
                'sms' => '+17867024093',
                'email' => 'homedelivery@homedeliverybr.com',
                'country_code' => 'US',
            ],
            'to_address' => [
                'company_name' => 'HERCO SUITE#100',
                'line1' => $order->recipient->address.' '.$order->recipient->street_no,
                'city' => $order->recipient->city,    //City validation required
                'state_province' => $order->recipient->state->code,
                'postal_code' => $order->recipient->zipcode,  //Zip validation required
                'phone_number' => '+13058885191',
                'country_code' => 'US', 
            ],
            'weight' => (float)$this->chargableWeight,
            'weight_unit' => ($order->measurement_unit == 'kg/cm') ? 'kg' : 'lb',
            'image_format' => 'pdf',
            'usps' => [
                'shape' => 'Parcel',
                'mail_class' => ($service == ShippingService::USPS_PRIORITY) ? 'Priority' : 'FirstClass',
                'image_size' => '4x6',
            ],
        ];

        return $request_body;
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

    // USPS BUY Label Logics
    public function getSenderPrice($order, $request)
    {
        $data = $this->makeRequestAttributeForSenderRates($order, $request);
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
                Log::info('USPS Error'. $e->getMessage());
                return (object) [
                    'success' => false,
                    'message' => $e->getMessage(),
                ];
           }

    }

    public function buyLabel($order, $request)
    {
        return $this->uspsApiCall($this->makeRequestAttributeForSenderRates($order, $request));
    }

    private function makeRequestAttributeForSenderRates($order, $request)
    {
        if(!isset($request->uspsBulkLabel))
        {
            $this->calculateVolumetricWeight($order);
        }
        
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
            'to_address' => [
                'company_name' => 'HERCO SUITE#100',
                'line1' => '2200 NW 129TH AVE',
                'city' => 'Miami',
                'state_province' => 'FL',
                'postal_code' => '33182',
                'phone_number' => '+13058885191',
                'country_code' => 'US', 
            ],
            'weight' => ($this->chargableWeight != null) ? (float)$this->chargableWeight : (float)$order->weight,
            'weight_unit' => ($order->measurement_unit == 'kg/cm') ? 'kg' : 'lb',
            'image_format' => 'pdf',
            'usps' => [
                'shape' => 'Parcel',
                'mail_class' => ($request->service == ShippingService::USPS_FIRSTCLASS || $request->service == 'FirstClass') ? 'FirstClass' : 'Priority',
                'image_size' => '4x6',
            ],
        ];

        return $request_body;
    }
}