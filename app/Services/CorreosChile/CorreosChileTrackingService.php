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
        $this->baseUri = 'http://hd-v2.test';
    }

    public function trackOrder($trackingNumber)
    {
       try {
           
            $response = Http::withBasicAuth($this->user, $this->password)->get($this->apiUrl.$trackingNumber);
            dd($response);
       } catch (Exception $e) {
           dd($e);
       }
    }
}