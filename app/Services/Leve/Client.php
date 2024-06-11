<?php

namespace App\Services\Leve;

use App\Services\Leve\Models\BaseModel;
use App\Services\Leve\Models\Package;
use Exception;
use GuzzleHttp\Client as GuzzleHttpClient;

class Client
{

    private $httpClient;
    private $baseUri;
    private $headers;

    public function __construct()
    {
        // $token = setting('LEVE_AUTHORIZE_KEY');
        $clientId = '562b8024-d3ba-456c-aa06-5fe04d2dc6d2';
        $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1aWQiOiIyMDExMWRjZC01ZTUyLTRjMDYtOTQ1Ni02NDU0NTUwZGYxYjYiLCJpYXQiOjE1OTQ5MjMwOTN9.C47_EbGQtJZEyYHwjfQMeK2UTd2XkeT07Y-eArr8np0';

        if ( app()->environment('production') ){
            $this->baseUri = 'https://api.leveexpress.com/api';
        }else{
            // $this->baseUri = 'https://sandbox-api.leveexpress.com/api';
            $this->baseUri = 'https://api.leveexpress.com/api';
        }

        $this->httpClient = new GuzzleHttpClient();

        $this->headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'MerchantID' => $clientId,
            'Authorization' => 'Bearer '.$token
        ];
    }

    
    /**
     * @param $package,
     * @return $data,
     */
    public function createPackage(Package $package)
    {
        try {
            $response = $this->httpClient->post(
                $this->getUrl('/packages/create'),[
                    'json' =>  $package->toArray(),
                    'headers' => $this->headers
                ]
            );

            if ( $response->getStatusCode() == 201 ){
                $data = json_decode($response->getBody()->getContents());
                
                return (Object)[
                    'success' => true,
                    'data' => $data
                ];
            }

            throw new \Exception($response->getBody()->getContents(),500);

        }catch (\GuzzleHttp\Exception\ClientException $e) {
            return (Object)[
                'success' => false,
                'message' => $e->getResponse()->getBody()->getContents(),
                'data' => json_decode($e->getResponse()->getBody()->getContents())
            ];
        } catch (\Exception $ex) {
            return (Object)[
                'success' => false,
                'message' => $ex->getMessage(),
                'data' => json_decode($ex->getMessage())
            ];
        }
    }

    /**
     * @param $trackingCode,
     * @return $package,
     */
    public function getPackage($trackingCode)
    {
        try {

            $response = $this->httpClient->get(
                $this->getUrl('/operation/get-package-details/'.$trackingCode),[
                    'headers' => $this->headers
                ]
            );

            if ( $response->getStatusCode() == 200 ){
                $data = json_decode($response->getBody()->getContents(),true);
                
                return (Object)[
                    'success' => true,
                    'data' => (new BaseModel($data))
                ];
            }

            throw new Exception($response->getBody()->getContents(),500);

        } catch (\Exception $ex) {
            return (Object)[
                'success' => false,
                'message' => $ex->getMessage()
            ];
        }
    }

    public function downloadCN23($url)
    {
        try {

            $response = $this->httpClient->get(
                $url,[
                    'headers' => $this->headers
                ]
            );

            if ( $response->getStatusCode() == 200 ){
                return (Object)[
                    'success' => true,
                    'data' => $response->getBody()->getContents()
                ];
            }

            throw new \Exception($response->getBody()->getContents(),500);

        }catch (\GuzzleHttp\Exception\ClientException $e) {
            return (Object)[
                'success' => false,
                'message' => $e->getResponse()->getBody()->getContents()
            ];
        } catch (\Exception $ex) {
            return (Object)[
                'success' => false,
                'message' => $ex->getMessage()
            ];
        }
    }

    public function getUrl($url)
    {
        return $this->baseUri.$url;
    }

}
