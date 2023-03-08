<?php

namespace App\Services\PostPlus;

use App\Models\Warehouse\Container;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client as GuzzleClient;

class PostPlusShipment
{
    protected $baseUri;
    protected $container;
    protected $apiKey;

    public function __construct(Container $container)
    {
        if (app()->isProduction()) {
            $this->apiKey = config('postplus.production.x-api-key');
            $this->baseUri = config('postplus.production.base_uri');
        } else {
            $this->apiKey = config('postplus.test.x-api-key');
            $this->baseUri = config('postplus.test.base_uri');
        }
        $this->container = $container;
        $this->client = new GuzzleClient(['verify' => false]);
    }

    private function getHeaders()
    {
        return [ 
            'x-api-key' => $this->apiKey,
            'Content-Type' => 'application/json'
        ];
    }

    public function create()
    {
        //dd($this->container->orders);
        $url = $this->baseUri . '/shipments';
        if($this->container->awb) {
            $body = [
                "type" => "AWB",
                "terminalCode" => "PDL",
                "shipmentNr" => '133-45916161',
                'arrivalInfo' => [
                    'transportNr' => $this->container->dispatch_number,
                    'originCountryCode' => "US",
                    'totalWeight' => $this->container->getWeight(),
                    'totalBags' => 1,
                    'arrivalOn' => "2023-03-02 08:00:00",
                    'notes' => ''
                 ],
            ];
            $response = Http::withHeaders($this->getHeaders())->post($url, $body);
            $data= json_decode($response);
    
            if ($response->successful()) { 
                if ($data->id) {
                    return $this->addParcels($this->container->orders, $data->id);
                } else {
                    return $this->responseUnprocessable($data->detail);
                }
            } else {
                return $this->responseUnprocessable($data->detail);
            }
        }
        else {
            return $this->responseUnprocessable("Airway Bill Number is Required for Processing.");
        }
    }

    public function addParcels($items, $id)
    {
        $codes = [];
        foreach ($this->container->orders as $key => $item) {
            $itemToPush = [];
            $codesToPush = [
                $item->corrios_tracking_code,
            ];
           array_push($codes, $codesToPush);
        }
        $parcels = implode(",", array_merge(...$codes));
        $url = $this->baseUri . '/parcels/update-references-many';
        $body = [
            "parcelNrs" =>  $parcels,
            "updateBagNr" => $this->container->seal_no,
            "linkShipmentId" => $id
        ];
        $response = Http::withHeaders($this->getHeaders())->post($url, $body);
        $data= json_decode($response);
        // dd($data);
        if ($response->successful() && !is_null($data->parcels)) {
            return $this->prepareShipment($id);
        } else {
            return $this->responseUnprocessable($data->detail);
        }
    }

    public function prepareShipment($id)
    {
        $url = $this->baseUri . "/shipments/$id/prepare";
        $body = [ "" => '', ];
        $response = Http::withHeaders($this->getHeaders())->post($url, $body);
        $data= json_decode($response);
        // dd($url, $data, $id);
        if ($response->successful() && optional($data)->shipmentSubmitToken) {
            return $this->submitShipment($data->shipmentSubmitToken, $id);
        } else {
            return $this->responseUnprocessable("No parcels in shipment");
        }
    }

    public function submitShipment($token, $id)
    {
        $url = $this->baseUri . "/shipments/$id/submit";
        $body = [
            "shipmentSubmitToken" =>  $token,
        ];
        $response = Http::withHeaders($this->getHeaders())->post($url, $body);
        $data= json_decode($response);
        if ($response->successful()) {
            return $this->getShipmentDetails($id);
        } else {
            return $this->responseUnprocessable($data->detail);
        }
    }

    public function getShipmentDetails($id)
    {
        $url = $this->baseUri . "/shipments/$id?IncludeBags=true&IncludeDocuments=true&IncludeManifestFiles=true";
        $response = $this->client->get($url,['headers' => $this->getHeaders()]);
        $data = json_decode($response->getBody()->getContents());
        if ($data->bags) {
            return $this->responseSuccessful($data, 'Shipment Created Successfully');
        } else {
            return $this->responseUnprocessable($data->detail);
        }
    }

    public function getLabel($id)
    {
        $url = $this->baseUri . "/documents/shipments/$id/all-documents";
        $response = $this->client->get($url,['headers' => $this->getHeaders()]);
        // $response = Http::withHeaders($this->getHeaders())->get($url, $body);
        $data = json_decode($response->getBody()->getContents());
        dd($data);
        $data= json_decode($response);
        if ($response->successful()) {
            return $this->responseSuccessful($data, '');
        } else {
            return $this->responseUnprocessable($data->detail);
        }
    }

    public static function responseUnprocessable($message)
    {
        return response()->json([
            'isSuccess' => false,
            'message' => $message,
        ], 422);
    }
    public static function responseSuccessful($output, $message)
    {
        return response()->json([
            'isSuccess' => true,
            'output' => $output,
            'message' =>  $message,
        ]);
    }
}
