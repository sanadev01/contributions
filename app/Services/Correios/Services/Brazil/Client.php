<?php

namespace App\Services\Correios\Services\Brazil;

use App\Models\Order;
use App\Models\OrderTracking;
use Illuminate\Support\Facades\Cache;
use App\Models\Warehouse\DeliveryBill;
use GuzzleHttp\Client as GuzzleClient;
use App\Services\Correios\Contracts\Package;
use App\Services\Correios\Contracts\Container;
use App\Services\Correios\Models\PackageError;
use App\Services\Correios\Services\Brazil\GetServiceToken;
use App\Services\Correios\Services\Brazil\cn23\CorreiosOrder;

class Client
{
    protected $client;
    private $baseUri;

    public function __construct()
    {
        $this->baseUri = 'https://api.correios.com.br';
        $this->client = new GuzzleClient([
            'base_uri' => $this->baseUri
        ]);
    }

    public function createPackage(Package $order)
    {
        $packet = new CorreiosOrder($order);
        \Log::info(
            $packet
        );
        try {
            $response = $this->client->post('/packet/v1/packages', [
                'headers' => [
                    'Authorization' => (new GetServiceToken($order))->getBearerToken()
                ],
                'json' => [
                    'packageList' => [
                        $packet
                    ]
                ]
            ]);

            $data = json_decode($response->getBody()->getContents());
            $trackingNumber = $data->packageResponseList[0]->trackingNumber;

            if ($trackingNumber) {
                $order->update([
                    'corrios_tracking_code' => $trackingNumber,
                    'cn23' => [
                        "tracking_code" => $trackingNumber,
                        "stamp_url" => route('warehouse.cn23.download', $order->id),
                        'leve' => false
                    ],
                ]);

                // \Log::info('Response');
                // \Log::info([$data]);
                // store order status in order tracking
                return $this->addOrderTracking($order);
            }
            return null;
        } catch (\GuzzleHttp\Exception\ClientException $e) {

            $responseError = $e->getResponse()->getBody()->getContents();
            $errorCopy = new PackageError($responseError);
            $errorMessage = $errorCopy->getErrors();
            if ($errorMessage == "GTW-006: Token inválido." || $errorMessage == "GTW-007: Token expirado.") {
                // \Log::info('Token refresh automatically');
                Cache::forget('anjun_token');
                Cache::forget('bcn_token');
                Cache::forget('token');
                return $this->createPackage($order);
            }

            $error = new PackageError($responseError);
            return $error;
        } catch (\Exception $exception) {
            return new PackageError($exception->getMessage());
        }
    }

    public function createContainer(Container $container)
    {
        try {

            $response = $this->client->post('/packet/v1/units', [
                'headers' => [
                    'Authorization' => (new GetServiceToken($container->orders()->first()))->getBearerToken(),
                ],
                'json' => [
                    "dispatchNumber" => $container->dispatch_number,
                    "originCountry" => $container->origin_country,
                    "originOperatorName" => $container->origin_operator_name,
                    "destinationOperatorName" => $container->destination_operator_name,
                    "postalCategoryCode" => $container->postal_category_code,
                    "serviceSubclassCode" => $container->subclass_code,
                    "unitList" => [
                        [
                            "sequence" => $container->sequence,
                            "unitType" => $container->unit_type,
                            "trackingNumbers" => $container->orders->pluck('corrios_tracking_code')->toArray()
                        ]
                    ]
                ]
            ]);

            $data = json_decode($response->getBody()->getContents());
            return $data->unitResponseList[0]->unitCode;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return new PackageError($e->getResponse()->getBody()->getContents());
        } catch (\Exception $exception) {
            return new PackageError($exception->getMessage());
        }
    }

    public function registerDeliveryBill(DeliveryBill $deliveryBill)
    {
        try {
            $response = $this->client->post('/packet/v1/cn38request', [
                'headers' => [
                    'Authorization' => (new GetServiceToken($deliveryBill->containers()->first()->orders()->first()))->getBearerToken(),
                ],
                'json' => [
                    'dispatchNumbers' => $deliveryBill->containers->pluck('dispatch_number')->toArray()
                ]
            ]);

            $data = json_decode($response->getBody()->getContents());
            return $data->requestId;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return new PackageError($e->getResponse()->getBody()->getContents());
        } catch (\Exception $exception) {
            return new PackageError($exception->getMessage());
        }
    }

    public function getDeliveryBillStatus(DeliveryBill $deliveryBill)
    {
        try {
            $response = $this->client->get("/packet/v1/cn38request?requestId={$deliveryBill->request_id}", [
                'headers' => [
                    'Authorization' => (new GetServiceToken($deliveryBill->containers()->first()->orders()->first()))->getBearerToken(),
                ]
            ]);

            $data = json_decode($response->getBody()->getContents());

            if ($data->requestStatus == 'Error') {
                throw new \Exception($data->errorMessage);
            }

            return $data->requestStatus == 'Success' ? $data->cn38Code : null;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return new PackageError($e->getResponse()->getBody()->getContents());
        } catch (\Exception $exception) {
            return new PackageError($exception->getMessage());
        }
    }

    public function addOrderTracking($order)
    {
        if ($order->trackings->isEmpty()) {
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

    public function destroy($container)
    {
        try {
            $response = $this->client->delete("/packet/v1/units/dispatch/$container->dispatch_number", [
                'headers' => [
                    'Authorization' => (new GetServiceToken($container->orders()->first()))->getBearerToken()
                ]
            ]);
            return $response;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return new PackageError($e->getResponse()->getBody()->getContents());
        } catch (\Exception $exception) {
            return new PackageError($exception->getMessage());
        }
    }

    public function unitInfo($url, $request)
    {
        try {
            $token = (new GetServiceToken())->getToken();
            if ($request->api == 'anjun') {
                $token = (new GetServiceToken())->getAnjunToken();
            }
            if ($request->api == 'bcn') {
                $token = (new GetServiceToken())->getBCNToken();
            }
            if ($request->type == 'departure_info') {
                $response = $this->client->put($url, [
                    'headers' => [
                        'Authorization' => $token
                    ],
                    'json' => [
                        "unitCodeList" => [
                            $request->unitCode
                        ],
                        "flightNumber" => $request->flightNo,
                        "airlineCode" => $request->airlineCode,
                        "departureDate" => $request->start_date,
                        "departureAirportCode" => $request->deprAirportCode,
                        "arrivalDate" => $request->end_date,
                        "arrivalAirportCode" => $request->arrvAirportCode,
                        "destinationCountryCode" => $request->destCountryCode,
                    ]
                ]);
            } elseif ($request->type == 'departure_cn38') {
                $json = array(
                    "cn38CodeList" => array_map('trim', explode(",", $request->unitCode)),
                    "flightList" => array(
                        array(
                            "flightNumber" => $request->flightNo,
                            "airlineCode" => $request->airlineCode,
                            "departureDate" => $request->start_date,
                            "departureAirportCode" => $request->deprAirportCode,
                            "arrivalDate" => $request->end_date,
                            "arrivalAirportCode" => $request->arrvAirportCode
                        )
                    )
                );
                $response = $this->client->put(
                    $url,
                    [
                        'headers' => [
                            'Authorization' => $token,

                            'Content-Type' => 'application/json',
                        ],
                        'json' =>  $json
                    ]
                );
                if ($response->getStatusCode() === 200) {
                    // \Log::info('Departure confirm successfully');
                    // \Log::info(explode(",",$request->unitCode));
                    session()->flash('alert-success','Departure confirm successfully'); 
                    return json_decode('Departure confirm successfully'); 
                }
            } else {
                $response = $this->client->get($url, [
                    'headers' =>  [
                        'Authorization' => $token
                    ],
                ]);
            }

            return json_decode($response->getBody()->getContents());
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return new PackageError($e->getResponse()->getBody()->getContents());
        } catch (\Exception $exception) {

            return new PackageError($exception->getMessage());
        }
    }

    public function getModality($trackingNumber)
    {
        try {
            $url = "/packet/v1/packages?trackingNumber=$trackingNumber";
            $response = $this->client->get($url, [
                'headers' =>  [
                    'Authorization' => (new GetServiceToken(null, $trackingNumber))->getBearerToken()
                ],
            ]);

            $modality = json_decode($response->getBody()->getContents());

            return optional(optional($modality->packageList)[0])->distributionModality;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return new PackageError($e->getResponse()->getBody()->getContents());
        } catch (\Exception $exception) {
            return new PackageError($exception->getMessage());
        }
    }
}
