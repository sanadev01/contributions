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
            
        }catch (Exception $e) {
            return (object) [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}