<?php

namespace App\Services\Correios\Services\Brazil;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderTracking;
use Illuminate\Support\Facades\Cache;
use App\Models\Warehouse\DeliveryBill;
use GuzzleHttp\Client as GuzzleClient;
use App\Services\Converters\UnitsConverter;
use App\Services\Correios\Contracts\Package;
use App\Services\Correios\Contracts\Container;
use App\Services\Correios\Models\PackageError;
use App\Services\Correios\Contracts\PacketItem;
use App\Services\Correios\Contracts\CN23Response;
use App\Services\Correios\Contracts\CN35Response;
use App\Services\Correios\Contracts\CN38Response;
use App\Services\Correios\Contracts\PackageResponse;
use App\Services\Correios\Contracts\SendHttpRequests;
use App\Services\Correios\Contracts\ContainerResponse;
use App\Services\Correios\Contracts\Package as PackageAlias;

class Client{

    protected $client;

    private $baseUri = 'https://api.correios.com.br';
    private $username = 'hercofreight';
    private $password = '150495ca';
    private $numero = '0075745313';

    private $anjun_username = 'anjun2020';
    private $anjun_password = 'anjun';
    private $anjun_numero = '0077053850';

    public function __construct()
    {
        $this->client = new GuzzleClient([
            'base_uri' => $this->baseUri
        ]);
    }

    public function getToken()
    {
        return Cache::remember('token',Carbon::now()->addHours(24),function (){
            $response = $this->client->post('/token/v1/autentica/cartaopostagem',[
                'auth' => [
                    $this->username,
                    $this->password
                ],
                'json' => [
                    'numero' => $this->numero
                ]
            ]);

            return $response->getStatusCode() == 201 ? optional(json_decode($response->getBody()->getContents()))->token : null;
        });

    }

    public function getAnjunToken()
    {
        return Cache::remember('anjun_token',Carbon::now()->addHours(24),function (){
            $response = $this->client->post('/token/v1/autentica/cartaopostagem',[
                'auth' => [
                    $this->anjun_username,
                    $this->anjun_password
                ],
                'json' => [
                    'numero' => $this->anjun_numero
                ]
            ]);

            return $response->getStatusCode() == 201 ? optional(json_decode($response->getBody()->getContents()))->token : null;
        });

    }

    public function createPackage(Package $order)
    {
        if($order->isWeightInKg()) {
            $weight = UnitsConverter::kgToGrams($order->getWeight('kg'));
        }else{
            $kg = UnitsConverter::poundToKg($order->getWeight('lbs'));
            $weight = UnitsConverter::kgToGrams($kg);
        }
        
        $packet = new \App\Services\Correios\Models\Package();

        $packet->customerControlCode = $order->id;
        $packet->senderName = $order->sender_first_name.' '.$order->sender_last_name;
        $packet->recipientName = $order->recipient->getFullName();
        $packet->recipientDocumentType = $order->recipient->getDocumentType();
        $packet->recipientDocumentNumber = cleanString($order->recipient->tax_id);
        $packet->recipientAddress = $order->recipient->address;
        $packet->recipientAddressComplement = $order->recipient->address2;
        $packet->recipientAddressNumber = $order->recipient->street_no;
        $packet->recipientZipCode = cleanString($order->recipient->zipcode);
        $packet->recipientState = $order->recipient->state->code;
//    $packet->recipientPhoneNumber = $order->recipient->phone;
        $packet->recipientEmail = $order->recipient->email;
        $packet->distributionModality = $order->getDistributionModality();
        $packet->taxPaymentMethod = $order->getService() == 1 ? 'DDP' : 'DDU';
        $packet->totalWeight =  ceil($weight);

        $width = round($order->isMeasurmentUnitCm() ? $order->width : UnitsConverter::inToCm($order->width));
        $height = round($order->isMeasurmentUnitCm() ? $order->height : UnitsConverter::inToCm($order->height));
        $length = round($order->isMeasurmentUnitCm() ? $order->length : UnitsConverter::inToCm($order->length));

        $packet->packagingWidth =  $width > 11 ? $width : 11;
        $packet->packagingHeight = $height > 2 ? $height : 2;
        $packet->packagingLength = $length > 16 ? $length : 16 ;

        $packet->freightPaidValue = $order->user_declared_freight;

        $items = [];

        foreach ($order->items as $item){
            $pItem = new PacketItem();
            $pItem->hsCode = $item->sh_code;
            $pItem->description = $item->description;
            $pItem->quantity = $item->quantity;
            $pItem->value = $item->value;

            $items[] = $pItem;
        }

        $packet->items = $items;

        \Log::info(
            $packet
        );
        
        try {
            $response = $this->client->post('/packet/v1/packages',[
               'headers' => [
                'Authorization' => ($order->shippingService->isAnjunService()) ? "Bearer {$this->getAnjunToken()}" :"Bearer {$this->getToken()}"
               ],
                'json' => [
                    'packageList' => [
                        $packet
                    ]
                ]
            ]);

            $data = json_decode($response->getBody()->getContents());
            $trackingNumber = $data->packageResponseList[0]->trackingNumber;

            if ( $trackingNumber ){
                $order->update([
                    'corrios_tracking_code' => $trackingNumber,
                    'cn23' => [
                        "tracking_code" => $trackingNumber,
                        "stamp_url" => route('warehouse.cn23.download',$order->id),
                        'leve' => false
                    ],
                ]);

                // store order status in order tracking
                return $this->addOrderTracking($order);
            }
            return null;
        }catch (\GuzzleHttp\Exception\ClientException $e) {
            return new PackageError($e->getResponse()->getBody()->getContents());
        }
        catch (\Exception $exception){
            return new PackageError($exception->getMessage());
        }
    }

    public function createContainer(Container $container)
    {
        try {
            $response = $this->client->post('/packet/v1/units',[
                'headers' => [
                    'Authorization' => ($container->hasAnjunService()) ? "Bearer {$this->getAnjunToken()}" : "Bearer {$this->getToken()}"
                ],
                'json' => [
                    "dispatchNumber" => $container->dispatch_number,
                    "originCountry" => $container->origin_country,
                    "originOperatorName" => $container->origin_operator_name,
                    "destinationOperatorName" => $container->destination_operator_name,
                    "postalCategoryCode" => $container->postal_category_code,
                    "serviceSubclassCode" => $container->getSubClassCode(),
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
        }catch (\GuzzleHttp\Exception\ClientException $e) {
            return new PackageError($e->getResponse()->getBody()->getContents());
        }
        catch (\Exception $exception){
            return new PackageError($exception->getMessage());
        }
    }

    public function registerDeliveryBill(DeliveryBill $deliveryBill)
    {
        try {
            $response = $this->client->post('/packet/v1/cn38request',[
                'headers' => [
                    'Authorization' => ($deliveryBill->containers()->first()->hasAnjunService()) ?  "Bearer {$this->getAnjunToken()}" : "Bearer {$this->getToken()}"
                ],
                'json' => [
                    'dispatchNumbers' => $deliveryBill->containers->pluck('dispatch_number')->toArray()
                ]
            ]);

            $data = json_decode($response->getBody()->getContents());
            return $data->requestId;
        }catch (\GuzzleHttp\Exception\ClientException $e) {
            return new PackageError($e->getResponse()->getBody()->getContents());
        }
        catch (\Exception $exception){
            return new PackageError($exception->getMessage());
        }
    }

    public function getDeliveryBillStatus(DeliveryBill $deliveryBill)
    {
        try {
            $response = $this->client->get("/packet/v1/cn38request?requestId={$deliveryBill->request_id}",[
                'headers' => [
                    'Authorization' => ($container->hasAnjunService()) ? "Bearer {$this->getAnjunToken()}" : "Bearer {$this->getToken()}"
                ]
            ]);

            $data = json_decode($response->getBody()->getContents());

            if ( $data->requestStatus == 'Error' ){
                throw new \Exception($data->errorMessage);
            }

            return $data->requestStatus == 'Success' ? $data->cn38Code : null;
        }catch (\GuzzleHttp\Exception\ClientException $e) {
            return new PackageError($e->getResponse()->getBody()->getContents());
        }
        catch (\Exception $exception){
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

    public function destroy($container)
    {
        try {
            $response = $this->client->delete("/packet/v1/units/dispatch/$container->dispatch_number",[
                'headers' => [
                    'Authorization' => ($container->hasAnjunService()) ? "Bearer {$this->getAnjunToken()}" : "Bearer {$this->getToken()}"
                ]
            ]);
            return $response;
        }catch (\GuzzleHttp\Exception\ClientException $e) {
            return new PackageError($e->getResponse()->getBody()->getContents());
        }
        catch (\Exception $exception){
            return new PackageError($exception->getMessage());
        }
    }

}
