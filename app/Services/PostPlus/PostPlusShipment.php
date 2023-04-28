<?php

namespace App\Services\PostPlus;

use Carbon\Carbon;
use App\Models\Warehouse\Container;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client as GuzzleClient;

class PostPlusShipment
{
    protected $baseUri;
    protected $container;
    protected $containers;
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
        $this->containers = Container::where('awb', $this->container->awb)->get();
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
        $url = $this->baseUri . '/shipments';
        $weight = 0;
        if($this->containers[0]->awb) {
            foreach($this->containers as $package) {
                $weight+= $package->getWeight();
            }
            $body = [
                "type" => "VirtualDespatch",
                "shipmentNr" => $this->containers[0]->awb,
                'arrivalInfo' => [
                    'transportNr' => $this->containers[0]->dispatch_number,
                    'originCountryCode' => "US",
                    'totalWeight' => $weight,
                    'totalBags' => count($this->containers),
                    'arrivalOn' => Carbon::now()->addDay(),
                    'notes' => ''
                 ],
            ];
            $response = Http::withHeaders($this->getHeaders())->post($url, $body);
            $data= json_decode($response);
    
            if ($response->successful()) { 
                if ($data->id) {
                    return $this->addParcels($data->id);
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

    public function addParcels($id)
    {
        $codes = [];
        foreach ($this->containers as $key => $container) {
            foreach ($container->orders as $key => $item) {
                $itemToPush = [];
                $codesToPush = [
                    $item->corrios_tracking_code,
                ];
                array_push($codes, $codesToPush);
            }
        }
        $parcels = implode(",", array_merge(...$codes));
        $url = $this->baseUri . '/parcels/update-references-many';
        $body = [
            "parcelNrs" =>  $parcels,
            "updateBagNr" => $this->containers[0]->seal_no,
            "linkShipmentId" => $id
        ];
        $response = Http::withHeaders($this->getHeaders())->post($url, $body);
        $data= json_decode($response);
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
        if ($response->successful() && optional($data)->shipmentSubmitToken) {
            return $this->submitShipment($data->shipmentSubmitToken, $id);
        } else {
            $this->deleteShipment($id);
            return $this->responseUnprocessable($data->status->warningDetails[1]);
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
            return $this->responseSuccessful($data, 'Container registration is successfull. Please donwload CN35 Label after 5 Mins');
        } else {
            return $this->responseUnprocessable($data->detail);
        }
    }

    public function getLabel($id)
    {
        $url = $this->baseUri . "/documents/shipment-documents/$id";
        return Http::withHeaders($this->getHeaders())->get($url);
    }

    public function getManifest($id)
    {
        $url = $this->baseUri . "/documents/shipments/$id/resulting-file?fileFormat=Csv";
        return Http::withHeaders($this->getHeaders())->get($url);
    }

    public function deleteShipment($id)
    {
        $url = $this->baseUri . "/shipments/$id";
        return Http::withHeaders($this->getHeaders())->delete($url);
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
