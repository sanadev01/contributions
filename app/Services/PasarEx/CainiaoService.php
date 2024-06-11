<?php
namespace App\Services\PasarEx;
 
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class CainiaoService
{
    protected $appKey;
    protected $appSecret;
    protected $preUrl;

    public function __construct()
    { 
        // Cainiao API credentials 
        $this->appKey = "681336";
        $this->appSecret = "07HYR3y1S33U48GS66fg5QEs8h84w5dve";
        $this->preUrl ="https://prelink.cainiao.com/gateway/link.do";
    }

    public function createOrder()
     {
        $data = [ 
        ];
 
        try { 

            $response = Http::withHeaders([
                'App-Key' => $this->appKey,
                'App-Secret' => $this->appSecret,
                'Content-Type' => 'application/json',
            ])->get($this->preUrl, $data);
    
            dd($response); 
        } catch (\Exception $e) {
            // Handle any exceptions
            return $e->getMessage();
        }
     }
}