<?php

namespace App\Services\TotalExpress\Services;

use App\Models\ShippingService;
use App\Models\Warehouse\Container;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client as GuzzleClient;


class TotalExpressMasterBox
{
    protected $email;
    protected $password;
    protected $baseURL;
    protected $container;
    protected $http;

    public function __construct(Container $container)
    {
        if (app()->isProduction()) {
            $this->email = config('total_express.production.container.email');
            $this->password = config('total_express.production.container.password');
            $this->baseURL = config('total_express.production.container.baseURL');
        } else {
            $this->email = config('total_express.test.container.email');
            $this->password = config('total_express.test.container.password');
            $this->baseURL = config('total_express.test.container.baseURL');
        }
        
        $this->container = $container;
        
        $this->client = new GuzzleClient();
        $authParams = [
            'email' => $this->email,
            'password' => $this->password
        ];

        $response = $this->client->post("$this->baseURL/authenticate/total/carrier", ['json' => $authParams]);
        $data = json_decode($response->getBody()->getContents());
        if ($data->auth_token) {
            $this->token = $data->auth_token;
        }
    }

    private function getHeaders()
    {
        return [
            'Authorization' => "Bearer {$this->token}",
            'Accept' => 'application/json'
        ];
    }

    public function requestMasterBox()
    {
        $codes = [];

        foreach ($this->container->orders as $key => $item) {
            array_push($codes, $item->corrios_tracking_code);
        }

        $url = $this->baseURL . '/v1/masterboxes';
        $body = ["package_numbers" => $codes];
        
        $request = Http::withHeaders($this->getHeaders())->post($url, $body);
        $response = json_decode($request);
        
        if ($response->status == "SUCCESS" && !is_null($response->data)) {
            return $this->consultCreateMasterBox($response->data->request_id);
        } else {
            return $this->responseUnprocessable($response->messages[0]);
        }
    }

    public function consultCreateMasterBox($id)
    {
        $url = $this->baseURL . "/v1/request_status/create_masterbox/$id";

        $request = Http::withHeaders($this->getHeaders())->get($url);
        $response= json_decode($request);

        if ($response->status == "SUCCESS" && optional($response)->data) {
            $this->container->update([
                'unit_response_list' => json_encode($response),
                'unit_code' => $response->data->reference,
                'response' => 1
            ]); 
            return $this->responseSuccessful($response, 'Container registration is successful. You can donwload CN35 Label');
        } else {
            return $this->responseUnprocessable($response->messages[0]);
        }
    }

    public function createFlight($request)
    {
        $boxNumbers = implode(",", array_merge(...$request->boxNumbers));
        $url = $this->baseURL . '/v1/flights';
        $body = ["box_numbers" => $boxNumbers];

        $request = Http::withHeaders($this->getHeaders())->post($url, $body);
        $response= json_decode($request);
 
        if ($response->status == "SUCCESS" && $response->data->flight_id) {
            return $this->updateFlightInformation($response->data->flight_id, $request);
        } else {
            return $this->responseUnprocessable($data->message);
        } 
    }

    public function updateFlightInformation($id, $request)
    {
        $url = $this->baseURL . "/v1/flights/$id";
        $body = [
            "departure_date" => $request->departure_date,
            "arrival_date" => $request->arrival_date,
            "departure_time" => $request->departure_time,
            "arrival_time" => $request->arrival_time,
            "airline" => $request->airline,
            "departure_airport" => $request->departure_airport,
            "arrival_airport" => $request->arrival_airport,
            "flight_number" => $request->flight_number,
            "mawb_number" => $request->mawb_number,
            "mawb_file_format" => $request->mawb_file_format,
            "mawb_file" => $request->mawb_file,
        ];
        $request = Http::withHeaders($this->getHeaders())->put($url, $body); 
        $response= json_decode($request);
        if ($response->status == "SUCCESS") {
            return $this->responseSuccessful('',  'Flight information successfully saved'); 
        } else {
            return $this->responseUnprocessable($response->messages[0]);
        }
    }

    public function getLabel()
    {
        $url = $this->baseURL . 'bagscan?op=getReceptacleLabel';
        $body = [
            "receptacleNo" => $this->container->unit_code,
        ];

        $response = $this->http->post($url, $body);
        $data= json_decode($response);

        if ($response->successful() && $data->status == 0) {
            return $this->responseSuccessful($data->pdfReceptacleLable,$data->message);
        } else {
            return $this->responseUnprocessable($data->message);
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
