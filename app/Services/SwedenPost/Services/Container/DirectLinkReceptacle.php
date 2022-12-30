<?php

namespace App\Services\SwedenPost\Services\Container;

use App\Models\Warehouse\Container;
use Illuminate\Support\Facades\Http;

class DirectLinkReceptacle
{

    //Sweden Post Parameters
    protected $userName;
    protected $password;
    protected $baseUrl;
    //Sweden Post Parameters End 
    protected $container;
    protected $http;
    public function __construct(Container $container)
    {

        if (app()->isProduction()) {
            $this->userName = config('prime5.production.container.userName');
            $this->password = config('prime5.production.container.password');
            $this->baseURL = config('prime5.production.container.baseURL');
        } else {
            $this->userName = config('prime5.test.container.userName');
            $this->password = config('prime5.test.container.password');
            $this->baseURL = config('prime5.test.container.baseURL');
        }
        $this->container = $container;
        $this->http = Http::withBasicAuth($this->userName, $this->password);
    }

    public function create()
    {
        return dd(333);
        $url = $this->baseURL . 'bagscan?op=createReceptacle';
        $body = [
            "dlOfficeCode" => "600",
            "eventId" => 18,
            "contentCode" => "006",
            "countryCode" => "BR",
            "receptacleType" => "01",
            "serviceCode" => "001"
        ];
        $response = $this->http->post($url, $body);

        if ($response->successful() && json_decode($response)->status == 0) {
            $responseJson = json_decode($response);
            if ($responseJson->receptacle && $responseJson->receptacle->receptacleNo) {
                return $this->responseSuccessful($responseJson->receptacle->receptacleNo, $responseJson->message);
            } else {

                return $this->responseUnprocessable($responseJson->message);
            }
        } else {
            return $this->responseUnprocessable(json_decode($response)->message);
        }
    }

    public function scanItem($item)
    {
        $url = $this->baseURL . 'bagscan?op=scanItem';
        $body = [
            "itemNo" =>  $item,
            "receptacleNo" => $this->container->unit_code
        ];
        $response = $this->http->post($url, $body);
        if ($response->successful() && json_decode($response)->status == 0) {
            return $this->responseSuccessful('', json_decode($response)->message);
        } else {
            return $this->responseUnprocessable(json_decode($response)->message);
        }
    }

    public function removeItem($item)
    {
        $url = $this->baseURL . 'bagscan?op=removeItem';
        $body = [
            "itemNo" =>  $item,
            "receptacleNo" => $this->container->unit_code
        ];
        $response = $this->http->post($url, $body);

 
        if ($response->successful() && json_decode($response)->status == 0) {
            return $this->responseSuccessful('', json_decode($response)->message);
        } else {
            return $this->responseUnprocessable(json_decode($response)->message);
        } 
    }

    public function close()
    {
        $url = $this->baseURL . 'bagscan?op=closeReceptacle';
        $body = [
            "receptacleNo"              => $this->container->unit_code,
            "receptacleWeight"          => $this->container->getWeight(),
            "receptacleCloseDatetime"   => date_format(new \DateTime(), 'YmdHi'),
        ];
        $response = $this->http->post($url, $body);



        if ($response->successful() && json_decode($response)->status == 0) {

        } else {
            return $this->responseUnprocessable(json_decode($response)->message);
        }
    }

    public function getLabel()
    {
        $url = $this->baseURL . 'bagscan?op=getReceptacleLabel';
        $body = [
            "receptacleNo" => $this->container->unit_code,
        ];

        $response = $this->http->post($url, $body);
        //dummy true response



        if ($response->successful() && json_decode($response)->status == 0) {
            return $this->responseSuccessful(json_decode($response)->pdfReceptacleLable,json_decode($response)->message);
        } else {
            return $this->responseUnprocessable(json_decode($response)->message);
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
