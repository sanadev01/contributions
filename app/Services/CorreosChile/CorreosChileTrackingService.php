<?php
namespace App\Services\CorreosChile;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client as GuzzleClient;


class CorreosChileTrackingService
{
    protected $apiUrl;
    protected $user;
    protected $password;
    protected $baseUri;

    public function __construct($apiUrl, $user, $password)
    {
        $this->apiUrl = $apiUrl;
        $this->user = $user;
        $this->password = $password;
    }

    public function trackOrder($trackingNumber)
    {
       try {
           
            $response = Http::withBasicAuth($this->user, $this->password)->get($this->apiUrl.$trackingNumber);

            if($response->getStatusCode() == 200) 
            {
                $response = $response->json();
                return (Object)[
                    'status' => true,
                    'message' => 'Order Found',
                    'data' => $response['historial'],
                ];
            }
            
       } catch (Exception $e) {
            
            return (Object)[
                'status' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ];
       }
    }
}