<?php

namespace App\Services\SwedenPost;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DirectLinkTrackingService
{

    public function trackOrder($trackingNumber)
    {
        try {
            $url = "https://api.directlink.com/responseStatus?itemNumbers=LB891180709SE";
            $response = Http::withHeaders(['TP-API-KEY' => '8fcbd7946d1d0886f5e6bce32d54b199f14113fe70eed818316c69b22024ada7'])->get($url);
            $xmlResponse = simplexml_load_string($response->getBody(),'SimpleXMLElement',LIBXML_NOCDATA);
            $jsonResponse = json_encode($xmlResponse);
            $data = json_decode($jsonResponse, true);
            if ($response->successful()) {
                return (Object)[
                    'status' => true,
                    'message' => 'Order Found',
                    'data' => $data,
                ];
            }elseif($response->clientError())
            {
                return (Object)[
                    'status' => false,
                    'message' => $data['error'],
                    'data' => null,
                ];    
            }elseif ($response->status() !== 200) 
            {
    
                return (object) [
                    'status' => false,
                    'message' => $data['message'],
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