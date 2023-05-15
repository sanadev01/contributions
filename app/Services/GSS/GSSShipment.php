<?php

namespace App\Services\GSS;

use Carbon\Carbon;
use App\Models\Warehouse\Container;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client as GuzzleClient;

class GSSShipment
{
    protected $userId;
    protected $password;
    protected $locationId;
    protected $workStationId;
    protected $baseUrl;
    protected $container;
    protected $containers;

    public function __construct(Container $container)
    {
        if(app()->isProduction()){
            $this->userId = config('gss.production.userId');
            $this->password = config('gss.production.password');
            $this->locationId = config('gss.production.locationId');
            $this->workStationId = config('gss.production.workStationId');
            $this->baseUrl = config('gss.production.baseUrl');
        }else{ 
            $this->userId = config('gss.test.userId');
            $this->password = config('gss.test.password');
            $this->locationId = config('gss.test.locationId');
            $this->workStationId = config('gss.test.workStationId');
            $this->baseUrl = config('gss.test.baseUrl');
        }

        $this->container = $container;
        $this->containers = Container::where('awb', $this->container->awb)->get();
        $this->client = new GuzzleClient(['verify' => false]);
        
    }

    private function getHeaders()
    {
        $authParams = [
            'userId' => $this->userId,
            'password' => $this->password,
            'locationId' => $this->locationId,
            'workStationId' => $this->workStationId,
        ];
        $response = $this->client->post("$this->baseUrl/Authentication/login",['json' => $authParams ]);
        $data = json_decode($response->getBody()->getContents());
        if($data->accessToken) {
            return [ 
                'Authorization' => "Bearer {$data->accessToken}",
                'Accept' => 'application/json'
            ];
        }
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
                "type" => "IPA",
                "dutiable" => true,
                "receptacleType" => "E",
                "foreignOECode" => "CWB",
                "countryCode" => "BR",
                "dateOfMailing" => Carbon::now(),
                "pieceCount" => '',
                "weightInLbs" => '',
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
            return $this->responseUnprocessable(optional(optional(optional($data)->status)->warningDetails)[0]);
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
