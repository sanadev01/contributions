<?php
namespace App\Services\USPS;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class UspsService
{
    protected $api_url;
    protected $delete_usps_label_url;
    protected $create_manifest_url;
    protected $email;
    protected $password;

    public function __construct($api_url, $delete_usps_label_url, $create_manifest_url, $email, $password)
    {
        $this->api_url = $api_url;
        $this->delete_usps_label_url = $delete_usps_label_url;
        $this->create_manifest_url = $create_manifest_url;
        $this->email = $email;
        $this->password = $password;
    }

    public function generateLabel($order)
    {
        $data = $this->make_request_attributes($order);
        
        $usps_response = $this->usps_ApiCall($data);
        
        return $usps_response;
    }
    
    public function make_request_attributes($order)
    {
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
            'weight' => (float)$order->weight,
            'weight_unit' => 'kg',
            'dimensions' => [
                'width' => (float)$order->width,
                'length' => (float)$order->length,
                'height' => (float)$order->height,
            ],
            'dimensions_unit' => 'cm',
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
}