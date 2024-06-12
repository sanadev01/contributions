<?php

namespace App\Services\Sinerlog;

use App\Models\Order;
use App\Models\Warehouse\Container;
use Storage;
use App\Services\Converters\UnitsConverter;
use App\Services\Sinerlog\Models\BaseModel;
use App\Services\Sinerlog\Models\Package;
use App\Services\Sinerlog\Models\Order as SinerlogOrder;
use App\Services\Sinerlog\Models\Seller as SinerlogSeller;
use App\Services\Sinerlog\Models\Product as SinerlogProducts;
use App\Services\Sinerlog\Models\Customer as SinerlogCustomer;
use App\Services\Sinerlog\Models\Bag as SinerlogBag;
use App\Services\Sinerlog\Models\CustomerShippingAddress as SinerlogCustomerSA;

use Exception;
use GuzzleHttp\Client as GuzzleHttpClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Client
{   
    private $baseUri = 'https://dev.easymundi.com/';
    private $clientUsername = 'fernando@fecisan.com';
    private $clientPassword = ',96(LcBL~HLPk4J@';
    private $clientSecret = '1VZlziQ8J2obtEg1NByxxnC4DJqq6BeXs2Lxgkry';

    public function __construct()
    {
        return "teste";
       
        $this->headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$this->token
        ];        
    }

    public function getToken(){

        return Cache::remember('token',Carbon::now()->addHours(12),function (){
            $client = new GuzzleHttpClient([
                'base_uri' => $this->baseUri
            ]);

            $response = $client->post('/oauth/token',[
                'json' =>  [
                    'grant_type' => 'password',
                    'client_id' => '1',
                    'client_secret' => $this->clientSecret,
                    'username' => $this->clientUsername,
                    'password' => $this->clientPassword,
                    'scope' => '*'
                ],
                'headers' => [
                    'Content-Type' => 'application/json'
                ]
            ]);
            //return json_decode($response->getBody()->getContents());
            return ($response->getStatusCode() == 200 || $response->getStatusCode() == 201) ? optional(json_decode($response->getBody()->getContents()))->access_token : null;
        });

    }
    
    /**
     * @param $package,
     * @return $data,
     */
    public function createPackage(Order $order)
    {
        
        $token = $this->getToken();

        $recipientAddress = $order->recipient;

        $width = round($order->isMeasurmentUnitCm() ? $order->width : UnitsConverter::inToCm($order->width));
        $height = round($order->isMeasurmentUnitCm() ? $order->height : UnitsConverter::inToCm($order->height));
        $length = round($order->isMeasurmentUnitCm() ? $order->length : UnitsConverter::inToCm($order->length));

        if($order->isWeightInKg()) {
            $weight = UnitsConverter::kgToGrams($order->getWeight('kg'));
        }else{
            $kg = UnitsConverter::poundToKg($order->getWeight('lbs'));
            $weight = UnitsConverter::kgToGrams($kg);
        }
        

        $orderSinerlog = new SinerlogOrder();

        // create product info
        $products = [];
        $package_description = '';
        $actual_item_description = '';
        foreach ( $order->items as $orderItem){
            $item = new SinerlogProducts();

            $item->code = $orderItem->id;
            $item->sh_code = $orderItem->sh_code;
            $item->name = $orderItem->description;
            $item->value = round((float)$orderItem->value,2);
            $item->qty = strval($orderItem->quantity);
            $item->photoBase64 = '';
            $products[] = $item;

            $sh_description = \DB::table('sh_codes')
                    ->select('description')
                    ->where('code', $orderItem->sh_code)
                    ->first();
            
            //prevent to add a repeated description
            if($actual_item_description != $sh_description->description){
                $package_description = $package_description.', '.trim(substr($sh_description->description,0,strpos($sh_description->description,'-',0)));
            }
            $actual_item_description = $sh_description->description;
        }


        // Create order info
        $orderSinerlog->description = substr($package_description,2,strlen($package_description));
        $orderSinerlog->orderNumber = strval($order->id);
        $orderSinerlog->externalId = $order->tracking_id;
        $orderSinerlog->weight = $weight;
        $orderSinerlog->height = $height;
        $orderSinerlog->width = $width;
        $orderSinerlog->length = $length;
        if ( $order->items()->where('contains_battery',true)->count() > 0 ){
            $orderSinerlog->classification = 'hazmat';
        } else {
            $orderSinerlog->classification = 'regular';
        }
        $orderSinerlog->totalAmount = $order->order_value;
        $orderSinerlog->currency = "USD";
        // Get sinerlog service alias
        $sinerlogAlias = \DB::table('shipping_services')
                    ->select('service_api_alias')
                    ->find($order->shipping_service_id);
                    
        $orderSinerlog->deliveryType = $sinerlogAlias->service_api_alias;

        switch ($order->tax_modality) {
            case 'ddp':
                $orderSinerlog->deliveryTax = '1';
                break;
            case 'ddu':
                $orderSinerlog->deliveryTax = '2';
                break;              
            default:
                $orderSinerlog->deliveryTax = '2';
                break;
        }

        // create seller info
        $sinerLogSeller = new SinerlogSeller();
        $sinerLogSeller->name = $order->sender_first_name.' '.$order->sender_last_name;
        $sinerLogSeller->document = $order->sender_taxId;
        $sinerLogSeller->email = $order->sender_email;
        $sinerLogSeller->phone = $order->sender_phone;


        $seller = [
            'name' => $sinerLogSeller->name,
            'document' => $sinerLogSeller->document,
            'email' => $sinerLogSeller->email,
            'phone' => $sinerLogSeller->phone
        ];

        // create customer info      
        $sinerLogCustomer = new SinerlogCustomer();
        $sinerLogCustomer->name = $recipientAddress->first_name.' '.$recipientAddress->last_name;
        $sinerLogCustomer->document = $recipientAddress->tax_id;
        $sinerLogCustomer->email = $recipientAddress->email;
        $sinerLogCustomer->phone = $recipientAddress->phone;

        $customer = [
            'name' => $sinerLogCustomer->name,
            'document' => $sinerLogCustomer->document,
            'email' => $sinerLogCustomer->email,
            'phone' => $sinerLogCustomer->phone
        ];

        // create customer addressinfo 
        $sinerLogCustomerSA = new SinerlogCustomerSA();
        $sinerLogCustomerSA->street = $recipientAddress->address;
        $sinerLogCustomerSA->street2 = '';
        $sinerLogCustomerSA->number = $recipientAddress->street_no;
        $sinerLogCustomerSA->complement = $recipientAddress->address2;
        $sinerLogCustomerSA->city = $recipientAddress->city;
        // get brazilian state code
        $state = \DB::table('states')
                    ->select('code')
                    ->where([
                        ['id', '=', $recipientAddress->state_id],
                        ['country_id', '=', $recipientAddress->country_id]
                    ])
                    ->first();
        
        $sinerLogCustomerSA->province = $state->code;
        $sinerLogCustomerSA->zipcode = $recipientAddress->zipcode;

        $shippingAddress = [
            'street' => $sinerLogCustomerSA->street,
            'street2' => $sinerLogCustomerSA->street2,
            'number' => $sinerLogCustomerSA->number,
            'complement' => $sinerLogCustomerSA->complement,
            'city' => $sinerLogCustomerSA->city,
            'province' => $sinerLogCustomerSA->province,
            'zipcode' => $sinerLogCustomerSA->zipcode
        ];
       

        $orderBody = [
            'description' => $orderSinerlog->description,
            'orderNumber' => $orderSinerlog->orderNumber,
            'externalId' => $orderSinerlog->externalId,
            'weight' => $orderSinerlog->weight,
            'height' => $orderSinerlog->height,
            'width' => $orderSinerlog->width,
            'length' => $orderSinerlog->length,
            'classification' => $orderSinerlog->classification,
            'totalAmount' => $orderSinerlog->totalAmount,
            'currency' => $orderSinerlog->currency,
            'deliveryType' => $orderSinerlog->deliveryType,
            'deliveryTax' => $orderSinerlog->deliveryTax,
            'seller' => $seller,
            'customer' => $customer,
            'shippingAddress' => $shippingAddress,
            'products' => $products    
        ];
        //dd($token);
        //dd(json_encode($orderBody));
        
        try {

            $client = new GuzzleHttpClient([
                'base_uri' => $this->baseUri
            ]);

            $response = $client->post('/api/orders',[
                'json' =>  $orderBody,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer '.$token 
                ]
            ]);
            //dd($response);
            if ( $response->getStatusCode() == 200 || $response->getStatusCode() == 201){
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

    public function getLabel(Order $order)
    {
        try {

            if( $order->sinerlog_url_label != '' ){
                return (Object)[
                    'success' => true,
                    'data' => $order->sinerlog_url_label
                ];
            } else {
                $token = $this->getToken();

                $client = new GuzzleHttpClient([
                    'base_uri' => $this->baseUri
                ]);
    
                $trxId = $order->sinerlog_tran_id;
    
                $response = $client->get('/api/orders/cn23/'.$trxId,[
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer '.$token 
                    ]
                ]);
                
                if ( $response->getStatusCode() == 200 || $response->getStatusCode() == 201){

                    $data = json_decode($response->getBody()->getContents());

                    $order->setSinerlogLabelURL($data->data->file);

                    return (Object)[
                        'success' => true,
                        'data' => $data->data->file
                    ];
                }
    
                throw new \Exception($response->getBody()->getContents(),500);
            }

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

    public function getUrl($url)
    {
        return $this->baseUri.$url;
    }

    public function createBag(Container $sinerlog_container)
    {
        $token = $this->getToken();

        $sinerlogBag = new SinerlogBag();
        $sinerlogBag->bag_code = 'TS'.$sinerlog_container->dispatch_number;
        $sinerlogBag->seal_barcode = $sinerlog_container->seal_no;
        $sinerlogBag->unitization_type = $sinerlog_container->unit_type;
        $sinerlogBag->weight = $sinerlog_container->getWeight() * 1000;

        foreach($sinerlog_container->orders as $key => $order)
        {
            array_push($sinerlogBag->orders, array('tracking_number' => $order->corrios_tracking_code));
        }

        $bagBody = [
            'bag_code' => $sinerlogBag->bag_code,
            'seal_barcode' => $sinerlogBag->seal_barcode,
            'unitization_type' => $sinerlogBag->unitization_type,
            'weight' => $sinerlogBag->weight,
            'orders' => $sinerlogBag->orders
        ];

        try {
            $client = new GuzzleHttpClient([
                'base_uri' => $this->baseUri
            ]);

            $response = $client->post('/api/bags',[
                'json' =>  $bagBody,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer '.$token 
                ]
            ]);
            
            if ( $response->getStatusCode() == 200 || $response->getStatusCode() == 201){
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

    public function getBagCN35(Container $sinerlog_container)
    {
        $token = $this->getToken();

        try {
            $client = new GuzzleHttpClient([
                'base_uri' => $this->baseUri
            ]);

            $response = $client->get('/api/bags/cn35/' . (int)$sinerlog_container->unit_code ,[
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer '.$token 
                ]
            ]);
            
            if ( $response->getStatusCode() == 200 || $response->getStatusCode() == 201){
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
}
