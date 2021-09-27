<?php

namespace App\Services\USPS;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class USPSTrackingService
{

    protected $apiUrl;
    protected $email;
    protected $password;

    public function __construct($apiUrl, $email, $password)
    {
        $this->apiUrl = $apiUrl;
        $this->email = $email;
        $this->password = $password;
    }

    public function trackOrder($trackingNumber)
    {
       try {

            $response = Http::withBasicAuth($this->email, $this->password)->get($this->apiUrl.$trackingNumber);

            if($response->successful())
            {
                return (Object)[
                    'status' => true,
                    'message' => 'Order Found',
                    'data' => $response->json(),
                ];
            }elseif($response->clientError())
            {
                return (Object)[
                    'status' => false,
                    'message' => $response->json()['error'],
                    'data' => null,
                ];    
            }elseif ($response->status() !== 200) 
            {
    
                return (object) [
                    'status' => false,
                    'message' => $response->json()['message'],
                    'data' => null,
                ];
            }
            
        }catch (Exception $e) {
            return (object) [
                'status' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ];
        }
    }
}