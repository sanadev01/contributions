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
        $this->url = config('prime5.trackUrl');
        $this->apiKey = config('prime5.trackApiKey');
    }

    public function trackOrders($trackingCodes)
    {
        $trackingNumbers = '';
        foreach ($trackingCodes as $key => $code) {
            if ($key == 0) {
                $trackingNumbers = $code;
            } else {
                $trackingNumbers .= '%7C' . $code;
            }
        }
        try {
            $response = Http::withHeaders(['TP-API-KEY' => $this->apiKey])->get($this->url . $trackingNumbers);
            $xmlResponse = simplexml_load_string($response->getBody(), 'SimpleXMLElement', LIBXML_NOCDATA);
            $jsonResponse = json_encode($xmlResponse);
            $data = json_decode($jsonResponse, true);
            if ($response->successful() && $data) {
                return (object)[
                    'status' => true,
                    'message' => 'Order Found',
                    'data' => $data,
                ];
            }
            if (empty($data)) {
                return (object)[
                    'status' => false,
                    'message' => "Client Server Error - Unable to get tracking from APi",
                    'data' => null,
                ];
            }
        } catch (Exception $e) {
            return (object) [
                'status' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ];
        }
    }
}