<?php
namespace App\Services\USPS;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Services\Calculators\WeightCalculator;

class UspsService
{
    protected $api_url;
    protected $delete_usps_label_url;
    protected $create_manifest_url;
    protected $email;
    protected $password;
    protected $get_price_url;
    protected $chargableWeight;

    public function __construct($api_url, $delete_usps_label_url, $create_manifest_url, $get_price_url, $email, $password)
    {
        $this->api_url = $api_url;
        $this->delete_usps_label_url = $delete_usps_label_url;
        $this->create_manifest_url = $create_manifest_url;
        $this->email = $email;
        $this->password = $password;
        $this->get_price_url = $get_price_url;
    }

    public function generateLabel($order)
    {
        $data = $this->make_request_attributes($order);
        
        $usps_response = $this->usps_ApiCall($data);
        
        return $usps_response;
    }
    
    public function make_request_attributes($order)
    {
        $this->calculateVolumetricWeight($order);

        $request_body = [
            'request_id' => 'HD-'.$order->id,
            'from_address' => [
                'company_name' => 'HERCO',
                'line1' => '2200 NW 129TH AVE',
                'city' => 'Miami',
                'state_province' => 'FL',
                'postal_code' => '33182',
                'phone_number' => '+13058885191',
                'sms' => '+17867024093',
                'email' => 'homedelivery@homedeliverybr.com',
                'country_code' => $order->sender_country->code,
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

    public function usps_ApiCall($data)
    {
        try {
            
            $response = Http::withBasicAuth($this->email, $this->password)->post($this->api_url, $data);


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

            return (object) [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function deleteUSPSLabel($tracking_number)
    {
        try {
            
            $response =  Http::withBasicAuth($this->email, $this->password)->delete($this->delete_usps_label_url.$tracking_number);
            
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

            $response = Http::withBasicAuth($this->email, $this->password)->post($this->create_manifest_url, $data);
           
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

            return (object) [
                'success' => false,
                'message' => $e->getMessage(),
            ];

        }
    }

    public function getPrice($order, $service)
    {
       $data = $this->make_rates_request_attributes($order, $service);
       
       try {

        $response = Http::acceptJson()->withBasicAuth($this->email, $this->password)->post($this->get_price_url, $data);
        
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

    public function make_rates_request_attributes($order, $service)
    {
        $this->calculateVolumetricWeight($order);

        $request_body = [
            'from_address' => [
                'company_name' => 'HERCO',
                'line1' => '2200 NW 129TH AVE',
                'city' => 'Miami',
                'state_province' => 'FL',
                'postal_code' => '33182',
                'phone_number' => '+13058885191',
                'sms' => '+17867024093',
                'email' => 'homedelivery@homedeliverybr.com',
                'country_code' => $order->sender_country->code,
            ],
            'to_address' => [
                'company_name' => 'HERCO',
                'line1' => $order->recipient->address.' '.$order->recipient->street_no,
                'city' => $order->recipient->city,    //City validation required
                'state_province' => '',
                'postal_code' => $order->recipient->zipcode,  //Zip validation required
                'phone_number' => '+13058885191',
                'country_code' => 'US', 
            ],
            'weight' => (float)$this->chargableWeight,
            'weight_unit' => ($order->measurement_unit == 'kg/cm') ? 'kg' : 'lb',
            'image_format' => 'pdf',
            'usps' => [
                'shape' => 'Parcel',
                'mail_class' => ($service == 3440) ? 'Priority' : 'FirstClass',
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
}