<?php

namespace App\Services\Anjun;

use App\Models\OrderTracking;
use GuzzleHttp\Client as GuzzleClient;
use App\Services\Anjun\Services\Package;
use App\Models\Order;
use App\Services\Anjun\Services\AnjunError;
use App\Services\Anjun\Services\BigPackage;
use App\Services\Correios\Models\PackageError;
use App\Services\Correios\Contracts\Container;

class AnjunClient
{

    protected $client;
    private   $baseUri;
    private   $username;
    private   $password;
    private   $bigPackageBaseURL;

    public function __construct()
    {
        if (app()->isProduction()) {
            // Anjun Api Production Environment Credentials
            $this->baseUri           = config('anjun.production.baseUri');
            $this->username          = config('anjun.production.username');
            $this->password          = config('anjun.production.password');
            $this->bigPackageBaseURL = config('anjun.production.bigPackageBaseURL');
        } else {
            // Anjun Api Testing Environemtn Credentials 
            $this->baseUri           = config('anjun.testing.baseUri');
            $this->username          = config('anjun.testing.username');
            $this->password          = config('anjun.testing.password');
            $this->bigPackageBaseURL = config('anjun.testing.bigPackageBaseURL');
        }
        $this->client  = new GuzzleClient([
            'base_uri' => $this->baseUri
        ]);
    }


    public function createPackage(Order $order)
    {
        $username = $this->username;
        $password = $this->password;

        $orderBody = [
            'username'   => $username,
            'password'   => $password,
            "fuwu"       => '974',     //line code
            'dp'         => '',        // store id
        ] + (new Package($order))->convertToChinese();
        try {
            $response = $this->client->post('/napic.asp', [
                'json'    =>  $orderBody,
                'headers' => [
                    'Content-Type' => 'application/json',
                ]
            ]);

            $responseContents = json_decode($response->getBody()->getContents());

            if ($responseContents->statusCode) {
                $trackingNumber = $responseContents->guahao;
                if ($trackingNumber) {
                    $order->update([
                        'corrios_tracking_code' => $trackingNumber,
                        'cn23'                  => [
                            "tracking_code" => $trackingNumber,
                            "stamp_url"     => route('warehouse.cn23.download', $order->id),
                            'leve'          => false
                        ],
                    ]);
                    return $this->addOrderTracking($order);
                }
            } else {
                return responseUnprocessable((new AnjunError($responseContents))->getErrors());
            }
            return responseSuccessful(null, 'Label Created');
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return responseUnprocessable((new PackageError($e->getResponse()->getBody()->getContents()))->getErrors());
        } catch (\Exception $exception) {
            return responseUnprocessable((new PackageError($exception->getMessage()))->getErrors());
        }
    }

    public function createContainer(Container $container)
    {
        $this->client  = new GuzzleClient([
            'base_uri' => $this->bigPackageBaseURL
        ]);
        try {
            $response = $this->client->put('/api/channel/apiBaxiPostXbag/createXBag', [
                'headers'      => [
                    'Content-Type' => 'application/json',
                ],
                'json' =>  (array) new BigPackage($container),
            ]);

            $responseContents = json_decode($response->getBody()->getContents());
            if ($responseContents->status == 200) {
                return $this->getCN35BarCode($responseContents->data);
            } else {
                return responseUnprocessable((new AnjunError($responseContents))->getErrors());
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return responseUnprocessable($e->getResponse()->getBody()->getContents());
        } catch (\Exception $exception) {

            return responseUnprocessable($exception->getMessage());
        }
    }

    public function getCN35BarCode($id)
    {
        $this->client  = new GuzzleClient([
            'base_uri' => $this->bigPackageBaseURL
        ]);
        try {
            $response = $this->client->get('/api/channel/apiBaxiPostXbag/yuBaoCN35?id=' . $id, [
                'headers'      => [
                    'Content-Type' => 'application/json',
                ],
            ]);

            $responseContents = json_decode($response->getBody()->getContents());

            if ($responseContents->status == 200) {
                 
                return responseSuccessful($responseContents->data, 'Label Printer Success');
            }
            else
                return responseUnprocessable((new AnjunError($responseContents))->getErrors());
        } catch (\GuzzleHttp\Exception\ClientException $e) {

            return responseUnprocessable($e->getResponse()->getBody()->getContents());
        } catch (\Exception $exception) {
            return responseUnprocessable($exception->getMessage());
        }
    }


    public function addOrderTracking($order)
    {
        if ($order->trackings->isEmpty()) {
            OrderTracking::create([
                'order_id'     => $order->id,
                'status_code'  => Order::STATUS_PAYMENT_DONE,
                'type'         => 'HD',
                'description'  => 'Order Placed',
                'country'      => ($order->user->country != null) ? $order->user->country->code : 'US',
                'city'         => 'Miami',
            ]);
        }

        return responseSuccessful(null, 'Label Created');
    }
}
