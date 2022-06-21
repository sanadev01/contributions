<?php

namespace App\Services\Bps;

use App\Services\Bps\Models\Parcel;
use Exception;
use GuzzleHttp\Client as GuzzleHttpClient;
use Illuminate\Validation\UnauthorizedException;
use function array_merge;
use function json_decode;

class Client{


    private $username;
    private $password;
    public static $token;
    private $headers;
    private $baseUri;

    public function __construct()
    {
        if ( app()->environment('production') ){
            $this->baseUri = 'https://bps.bringer.io/public/api/v2';
        }else{
            $this->baseUri = 'https://bps.bringer.dev/public/api/v2';
        }
        $this->username = 'hercoff';
        $this->password = 'Herco2019!';
        $this->headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ];
    }

    public function getToken()
    {
        try{
            $response = (new GuzzleHttpClient())->post(
                $this->getUrl('/auth/token.json'),[
                    'json' => [
                        'username' => $this->username,
                        'password' => $this->password
                    ],
                    'headers' => $this->headers
                ]
            );

            if ( $response->getStatusCode() == 200 ){
                $data = json_decode($response->getBody()->getContents());
                return $data;
            }

            throw new Exception($response->getBody()->getContents(),500);

        }catch(Exception $ex){
            return (Object)[
                'success' => false,
                'message' => $ex->getMessage()
            ];
        }
    }

    public function login()
    {
        $response = $this->getToken();
        if ( $response->success ){
            self::$token = $response->data;
            return self::$token;
        }
        
        throw new UnauthorizedException($response->message,401);

        // self::$token = \Cache::remember('bps-auth-token', now()->addMinutes(180), function () {
            

        // });

    }

    public function createParcel(Parcel $parcel)
    {
        try{
            $response = (new GuzzleHttpClient())->post(
                $this->getUrl('/create/parcel/complete.json'),[
                    'json' => $parcel->toArray(),
                    'headers' => array_merge($this->headers,[
                        'Authorization' => "Bearer ".self::$token
                    ])
                ]
            );

            if ( $response->getStatusCode() == 200 ){
                return new BpsResponse(json_decode($response->getBody()->getContents()));
            }

            return new BpsResponse(
                (Object)[
                    'success' => false,
                    'data' => null,
                    'errors' => [
                        $response->getBody()->getContents()
                    ]
                ]
            );

        }catch(Exception $ex){
            return new BpsResponse(
                (Object)[
                'success' => false,
                'data' => null,
                'errors' => [
                    $ex->getMessage()
                ]
            ]);
        }
    }

    public function getLable($parcelId)
    {
        try{
            $response = (new GuzzleHttpClient())->get(
                $this->getUrl('/get/parcel/labels.json'),[
                    'query' => [
                        'id' => $parcelId
                    ],
                    'headers' => array_merge($this->headers,[
                        'Authorization' => "Bearer ".self::$token
                    ])
                ]
            );

            if ( $response->getStatusCode() == 200 ){
                if ( $pdf = $response->getBody()->getContents() ){
                    return new BpsResponse( (Object)[
                        'success' => true,
                        'data' => $pdf
                    ] );
                }

                return  new BpsResponse($response->getBody()->getContents());
            }

            return new BpsResponse((Object)[
                'success' => false,
                'data' => null,
                'errors' => [
                    $response->getBody()->getContents()
                ]
            ]);

        }catch(Exception $ex){
            return new BpsResponse((Object)[
                'success' => false,
                'data' => null,
                'errors' => [
                    $ex->getMessage()
                ]
            ]);
        }
    }

    public function getUrl($url)
    {
        return $this->baseUri.$url;
    }

}
