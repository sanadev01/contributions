<?php

namespace App\Services\Cainiao;

use App\Models\Order;
use App\Models\OrderTracking;
use Illuminate\Support\Facades\Http; 
use GuzzleHttp\Client as GuzzleClient;
use App\Services\Cainiao\Services\Parcel; 
use App\Services\Correios\Contracts\Package; 
use App\Services\Correios\Models\PackageError;

class Client{

    protected $appSecret;
    protected $cpCode;
    protected $client;
    public $error;

    public function __construct()
    {   
        if(app()->isProduction()){
            $this->appSecret = config('cainiao.production.app_secret');
            $this->cpCode = config('cainiao.production.cp_code');
        }else{
            $this->appSecret = config('cainiao.production.app_secret');
            $this->cpCode = config('cainiao.production.cp_code');
        }

        $this->client = new GuzzleClient();
    } 

    // private function getHeaders()
    // {
    //     return [
    //         'Authorization' => 'Bearer ' . $this->token,
    //         'Content-Type' => 'application/json'
    //     ];
    // }
    public function createPackage(Order $order)
    {  
        $content = (new Parcel($order))->getRequestBody(); 
        $content =
        [
            "syncGetTrackingNumber" => true,
            "outOrderId" => "test20240626test",
            "receiverParam" => [
                "zipCode" => "25025102",
                "mobilePhone" => "+5521966321087",
                "city" => "Duque de Caxias",
                "countryCode" => "BR",
                "street" => "Avenida Henrique Valadares 1536",
                "district" => "",
                "name" => "Lucas Sibie Lucas Sibie",
                "detailAddress" => "Casa Parque Lafaiete",
                "telephone" => "+5521966321087",
                "state" => "RJ",
                "email" => "yvqbw91k97vpg0f@marketplace.amazon.com.br",
                "addressId" => ""
            ],
            "locale" => "zh_CN",
            "solutionParam" => [
                "importCustomsParam" => [
                    "taxNumber" => "088461089653333"
                ],
                "cainiaoCustomsParam" => [
                    "whetherNeed" => false
                ],
                "solutionCode" => "GM_OPEN_STD_CD"
            ],
            "packageParams" => [
                [
                    "itemParams" => [
                        [
                            "unitPrice" => 50,
                            "englishName" => "smart watch",
                            "itemType" => "cf_normal",
                            "clearanceShipUnitPrice" => 0,
                            "clearanceVat" => null,
                            "quantity" => 1,
                            "unitPriceCurrency" => "USD",
                            "hscode" => "8517629900",
                            "msds" => "",
                            "weight" => "200",
                            "clearanceShipVat" => null,
                            "clearanceUnitPrice" => null,
                            "itemId" => "C20-black",
                            "taxRate" => 0,
                            "taxCurrency" => "USD",
                            "chineseName" => "智能手表",
                            "itemUrl" => "aliexpress.com"
                        ]
                    ],
                    "length" => 16,
                    "width" => 11,
                    "height" => 2,
                    "weight" => "200"
                ]
            ],
            "senderParam" => [
                "zipCode" => "518109",
                "mobilePhone" => "",
                "city" => "shenzhen",
                "countryCode" => "CN",
                "street" => "",
                "district" => null,
                "name" => "Hrich",
                "detailAddress" => "Building 17, Fumao New Village,Sanlian Community",
                "telephone" => "18063210240",
                "state" => "GD",
                "email" => "hrich1@163.com",
                "addressId" => null
            ],
            "sourceHandoverParam" => [
                "type" => "PORT",
                "code" => "GRU"
            ]
        ];
        
        try {
        $linkUrl = 'https://link.cainiao.com/gateway/custom/open_integration_test_env';
        
        $msgType = 'cnge.order.create';    //调用的API名 
        $toCode = 'CNGCP-OPEN';        //调用的目标TOCODE，有些接口TOCODE可以不用填写  
        $digest = base64_encode(md5(json_encode($content) . $this->appSecret, true));     //生成签名
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $linkUrl);
        // For debugging 
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/x-www-form-urlencoded']);
        $post_data = 'msg_type='.$msgType
            . '&to_code='.$toCode
            . '&logistics_interface='.urlencode(json_encode($content))
            . '&data_digest='.urlencode($digest)
            . '&logistic_provider_id='.urlencode($this->cpCode);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_POST, 1);
        $output = curl_exec($ch);
        curl_close($ch); 
        $data = json_decode($output);  
        if($data->success)
        {
            $order->update([
                'corrios_tracking_code' => $data->data->trackingNumber,
                'cn23' => $data->data->trackingNumber,
                'api_response' => $output,
            ]); 
            return true;
        }else{
            $this->error = "Error code: $data->errorCode <br> Error message: $data->errorMsg";
            return false;
        }
        }catch (\Exception $exception){
            $this->error =$exception->getMessage();
            return false;
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

    
     

}
