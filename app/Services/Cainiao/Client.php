<?php

namespace App\Services\Cainiao;

use App\Models\Order;
use App\Models\OrderTracking;
use App\Models\Warehouse\DeliveryBill;
use App\Services\Cainiao\Services\Bigbag;
use App\Services\Cainiao\Services\CN38Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client as GuzzleClient;
use App\Services\Cainiao\Services\Parcel;
use App\Services\Cainiao\Services\UpdateParcel;
use App\Services\Correios\Contracts\Package;
use App\Services\Correios\Models\PackageError;

class Client
{

    protected $appSecret;
    protected $cpCode;
    protected $client;
    protected $appUrl;
    public $error;

    public function __construct()
    {
        if (app()->isProduction()) {
            $this->appUrl = config('cainiao.production.app_url');
            $this->appSecret = config('cainiao.production.app_secret');
            $this->cpCode = config('cainiao.production.cp_code');
        } else {
            $this->appUrl = config('cainiao.testing.app_url');
            $this->appSecret = config('cainiao.testing.app_secret');
            $this->cpCode = config('cainiao.testing.cp_code');
        }
        $this->client = new GuzzleClient();
    }
    public function createPackage(Order $order)
    {
        $content = (new Parcel($order))->getRequestBody();

        try {
            $msgType = 'cnge.order.create';    //调用的API名 
            $toCode = 'CNGCP-OPEN';        //调用的目标TOCODE，有些接口TOCODE可以不用填写  
            $digest = base64_encode(md5(json_encode($content) . $this->appSecret, true));     //生成签名
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->appUrl);
            // For debugging 
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_FAILONERROR, false);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/x-www-form-urlencoded']);
            $post_data = 'msg_type=' . $msgType
                . '&to_code=' . $toCode
                . '&logistics_interface=' . urlencode(json_encode($content))
                . '&data_digest=' . urlencode($digest)
                . '&logistic_provider_id=' . urlencode($this->cpCode);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_POST, 1);
            $output = curl_exec($ch);
            curl_close($ch);
            $data = json_decode($output);
            if ($data->success == 'true') {
                \Log::info([
                    'response' => $data,
                ]);
                $order->update([
                    'corrios_tracking_code' => $data->data->trackingNumber,
                    'cn23' => $data->data->trackingNumber,
                    'api_response' => $output,
                ]);
                return true;
            } else {
                $this->error = "Error code: $data->errorCode <br> Error message: $data->errorMsg";
                return false;
            }
        } catch (\Exception $exception) {
            $this->error = $exception->getMessage();
            return false;
        }
    }

    public function updatePackage(Order $order)
    {
        $content = (new UpdateParcel($order))->getRequestBody();
        try { 

            $msgType = 'cnge.order.update';    //调用的API名 
            $toCode = 'CNGCP-OPEN';        //调用的目标TOCODE，有些接口TOCODE可以不用填写  
            $digest = base64_encode(md5(json_encode($content) . $this->appSecret, true));     //生成签名
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->appUrl);
            // For debugging 
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_FAILONERROR, false);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/x-www-form-urlencoded']);
            $post_data = 'msg_type=' . $msgType
                . '&to_code=' . $toCode
                . '&logistics_interface=' . urlencode(json_encode($content))
                . '&data_digest=' . urlencode($digest)
                . '&logistic_provider_id=' . urlencode($this->cpCode);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_POST, 1);
            $output = curl_exec($ch);
            curl_close($ch);
            $data = json_decode($output);
            if ($data->success == 'true') {
                \Log::info([
                    'cainiao order tracking' => $order->corrios_tracking_code,
                    'cainiao order updated' => $data,
                ]);
                return true;
            } else {
                $this->error = "Error code: $data->errorCode <br> Error message: $data->errorMsg";
                return false;
            }
        } catch (\Exception $exception) {
            $this->error = $exception->getMessage();
            return false;
        }
    }

    function cngeBigbagCreate($container)
    {

        $content = (new Bigbag($container))->getRequestBody(); 
        $msgType = 'cnge.bigbag.create';  // 调用的API名 
        $toCode = 'CNPMS';        //  调用的目标TOCODE，有些接口TOCODE可以不用填写
        $digest = base64_encode(md5(json_encode($content) . $this->appSecret, true)); //生成签名   
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->appUrl);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/x-www-form-urlencoded']);
        $post_data = 'msg_type=' . $msgType
            . '&to_code=' . $toCode
            . '&logistics_interface=' . urlencode(json_encode($content))
            . '&data_digest=' . urlencode($digest)
            . '&logistic_provider_id=' . urlencode($this->cpCode);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_POST, 1);
        $output = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($output);
        \Log::info([
            'api cnge.bigbag.create output' =>  $data
        ]);
        if ($data->success == 'true') {
            $container->update([
                'unit_code' =>  $data->data->bigBagTrackingNumber,
                'response' => true,
                'unit_response_list' => $output
            ]);
            return true;
        } else {
            $this->error = "Error code: $data->errorCode <br> Error message: $data->errorMsg";
            return false;
        }
    }
    function cngeCn38Request(DeliveryBill $deliveryBill)
    {

        $content = (new CN38Request($deliveryBill))->getRequestBody(); 
        $msgType = 'cnge.cn38.request';  // 调用的API名 
        $toCode = 'CGOP';        //  调用的目标TOCODE，有些接口TOCODE可以不用填写
        $digest = base64_encode(md5(json_encode($content) . $this->appSecret, true)); //生成签名   
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->appUrl);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/x-www-form-urlencoded']);
        $post_data = 'msg_type=' . $msgType
            . '&to_code=' . $toCode
            . '&logistics_interface=' . urlencode(json_encode($content))
            . '&data_digest=' . urlencode($digest)
            . '&logistic_provider_id=' . urlencode($this->cpCode);
 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_POST, 1); 
        $output = curl_exec($ch);
        curl_close($ch);  
        $data = json_decode($output);
        \Log::info([
            'api cnge.cn38.request output' =>  $data
        ]);
        if ($data->success == 'true') {
            $deliveryBill->update([
                'cnd38_code' => null,
                'request_id' => null,
            ]);
            return true;
        } else {
            $this->error = "Error code: $data->errorCode <br> Error message: $data->errorMsg";
            return false;
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
}
