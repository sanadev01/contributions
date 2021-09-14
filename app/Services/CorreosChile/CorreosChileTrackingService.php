<?php
namespace App\Services\CorreosChile;

use Exception;
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

            dd($response->getStatusCode());
            if($response->getStatusCode() == 200) 
            {

                $response = $response->json();
                return (Object)[
                    'status' => 'success',
                    'data' => $response['historial'],
                ];
            }
            
       } catch (Exception $e) {
           dd($e);
       }
    }
}