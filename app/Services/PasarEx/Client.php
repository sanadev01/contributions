<?php

namespace App\Services\PasarEx;

use App\Models\Order;
use App\Models\OrderTracking;
use Illuminate\Support\Facades\Http; 
use GuzzleHttp\Client as GuzzleClient;
use App\Services\PasarEx\Services\Parcel; 
use App\Services\Correios\Contracts\Package; 
use App\Services\Correios\Models\PackageError;

class Client{

    protected $host;
    protected $baseUri;
    protected $client;

    protected $token;

    public function __construct()
    {   
        if(app()->isProduction()){
            $this->token = config('pasarex.production.token');
            $this->baseUri = config('pasarex.production.base_uri');
        }else{ 
            $this->token = config('pasarex.test.token');
            $this->baseUri = config('pasarex.test.base_uri');
        }

        $this->client = new GuzzleClient();
    } 

    private function getHeaders()
    {
        return [
            'Authorization' => 'Bearer ' . $this->token,
            'Content-Type' => 'application/json'
        ];
    }
    public function createPackage(Package $order)
    {  
        $shippingRequest = (new Parcel($order))->getRequestBody();
        try {
            $response = Http::withHeaders($this->getHeaders())->post("$this->baseUri/customs/consignment/store", $shippingRequest);
            $data = json_decode($response); 
            if($data->success){
                // $trackingNumber = $data->identifiers->parcelNr;
                // $printId = $data->prints[0]->id;
                // if($trackingNumber && $printId) {
                //     $getLabel = Http::withHeaders($this->getHeaders())->get("$this->baseUri/parcels/parcel-prints/get-many?ids=$printId&IncludeContents=true");
                //     $getLabelResponse = json_decode($getLabel);
                //     if(!$getLabelResponse->prints[0]->hasErrors) {
                //         $order->update([
                //             'corrios_tracking_code' => $trackingNumber,
                //             'api_response' => json_encode($getLabelResponse),
                //             'cn23' => [
                //                 "tracking_code" => $trackingNumber,
                //                 "stamp_url" => route('warehouse.cn23.download',$order->id),
                //                 'leve' => false
                //             ],
                //         ]);
                //         // store order status in order tracking
                //         return $this->addOrderTracking($order);
                //     }
                //     if($getLabelResponse->prints[0]->hasErrors) {
                //         return new PackageError("Error while print label. Code: ".optional(optional($getLabelResponse)->prints)->errorDetails[0]);
                //     }
                // }
                
                return new PackageError("<br> Code : $data->code <br> Message: $data->message ");

            } else{
                return new PackageError("Error while creating parcel <br> Code : $data->code <br> Message: $data->message ");
  
            }
             
            return null;
        }catch (\Exception $exception){
            return new PackageError($exception->getMessage());
        }
    }

    public function addOrderTracking($order)
    {
        if($order->trackings->isEmpty())
        {
            OrderTracking::create([
                'order_id' => $order->id,
                'status_code' => Order::STATUS_PAYMENT_DONE,
                'type' => 'HD',
                'description' => 'Order Placed',
                'country' => ($order->user->country != null) ? $order->user->country->code : 'US',
                'city' => 'Miami',
            ]);
        }
        return true;
    }

    
     

}
