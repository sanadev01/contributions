<?php

namespace App\Services\SwedenPost;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DirectLinkTrackingService
{
    protected $url;
    protected $apiKey;

    public function __construct()
    {
        if(app()->isProduction()){
            $this->url = config('prime5.production.trackUrl');
            $this->apiKey = config('prime5.production.trackApiKey');
        }else{ 
            $this->url = config('prime5.test.trackUrl');
            $this->apiKey = config('prime5.test.trackApiKey');
        }

    }

    public function trackOrder($trackingNumber)
    {
        try {

            $response = Http::withHeaders(['TP-API-KEY' => $this->apiKey])->get($this->url."LB891180709SE");
            $xmlResponse = simplexml_load_string($response->getBody(),'SimpleXMLElement',LIBXML_NOCDATA);
            $jsonResponse = json_encode($xmlResponse);
            $data = json_decode($jsonResponse, true);
            //dd($data);
            if ($response->successful() && $data) {
                return (Object)[
                    'status' => true,
                    'message' => 'Order Found',
                    'data' => $data,
                ];
            }
            if(empty($data))
            {
                return (Object)[
                    'status' => false,
                    'message' => "Client Server Error - Unable to get tracking from APi",
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