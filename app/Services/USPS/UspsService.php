<?php
namespace App\Services\USPS;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class UspsService
{
    protected $api_url;
    protected $email;
    protected $password;

    public function __construct($api_url, $email, $password)
    {
        $this->api_url = $api_url;
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
            'request_id' => 'XHA829122',
            'from_address' => [
                'company_name' => 'Ikonic',
                'line1' => '247 High St',
                'city' => 'Palo Alto',
                'state_province' => 'CA',
                'postal_code' => '94301',
                'phone_number' => '+6503915169',
                'sms' => 'SMS4440404',
                'email' => 'harry@redbrick247.com',
                'country_code' => 'US',
            ],
            'to_address' => [
                'first_name' => $order->recipient->first_name,
                'last_name' => $order->recipient->last_name,
                'line1' => $order->recipient->address.' '.$order->recipient->street_no,
                'city' => 'Palo Alto',    //City validation required
                'state_province' => $order->recipient->state->code,
                'postal_code' => '94301',  //Zip validation required
                'phone_number' => $order->recipient->phone,
                'country_code' => 'US', 
            ],
            'weight' => $order->weight,
            'weight_unit' => 'kg',
            'dimensions' => [
                'width' => $order->width,
                'length' => $order->length,
                'height' => $order->height,
            ],
            'dimensions_unit' => 'cm',
            'value' => $order->order_value,
            'image_format' => 'pdf',
            'image_resolution' => 300,
            'usps' => [
                'shape' => 'Parcel',
                'mail_class' => 'Priority',
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
                // dd($response);
                // $label = $response['base64_labels'][0];
                // $pdf = base64_decode($label);
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