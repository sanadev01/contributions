<?php

namespace App\Services\Cainiao;

use App\Models\Order;
use App\Models\OrderTracking;
use App\Models\Warehouse\DeliveryBill;
use App\Services\Cainiao\Services\AirlineArrive;
use App\Services\Cainiao\Services\AirlineReceive;
use App\Services\Cainiao\Services\Bigbag;
use App\Services\Cainiao\Services\CN38Request;
use App\Services\Cainiao\Services\Parcel;
use App\Services\Cainiao\Services\UpdateParcel;
use App\Services\Correios\Models\PackageError;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Log;

class Client
{
    protected $appSecret;
    protected $cpCode;
    protected $client;
    protected $appUrl;
    public $error;

    public function __construct()
    { 
        $this->client = new GuzzleClient;

        if (app()->isProduction()) {
            $this->appUrl = config('cainiao.production.app_url');
            $this->appSecret = config('cainiao.production.app_secret');
            $this->cpCode = config('cainiao.production.cp_code');
        } else {
            $this->appUrl = config('cainiao.testing.app_url');
            $this->appSecret = config('cainiao.testing.app_secret');
            $this->cpCode = config('cainiao.testing.cp_code');
        }
    }

    protected function generateDigest($content)
    {
        return base64_encode(md5(json_encode($content) . $this->appSecret, true));
    }

    protected function sendRequest($msgType, $toCode, $content)
    {
        $digest = $this->generateDigest($content);
        $postData = [
            'msg_type' => $msgType,
            'to_code' => $toCode,
            'logistics_interface' => json_encode($content),
            'data_digest' => $digest,
            'logistic_provider_id' => $this->cpCode
        ];

        try {
            $response = $this->client->post($this->appUrl, [
                'form_params' => $postData,
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded']
            ]);
            Log::info([
                "msgType" => $msgType,
                "createPackage: content" => $content,
                "createPackage: response data" => $response,
            ]);
            return json_decode($response->getBody());
        } catch (\Exception $exception) {
            $this->error = $exception->getMessage();
            return false;
        }
    }

    public function createPackage(Order $order)
    {
        $content = (new Parcel($order))->getRequestBody();
        $data = $this->sendRequest('cnge.order.create', 'CNGCP-OPEN', $content);

        if ($data && $data->success === 'true') {
           
            $order->update([
                'corrios_tracking_code' => $data->data->trackingNumber,
                'cn23' => $data->data->trackingNumber,
                'api_response' => json_encode($data),
            ]);
            $this->updatePackage($order->fresh());
            return true;
        }

        $this->error = "Error code: {$data->errorCode} <br> Error message: {$data->errorMsg}";
        return false;
    }

    public function updatePackage(Order $order)
    {
        if(!$order->corrios_tracking_code){
            return $this->createPackage($order);
        }
        $content = (new UpdateParcel($order))->getRequestBody();
        $data = $this->sendRequest('cnge.order.update', 'CNGCP-OPEN', $content);

        if ($data && $data->success === 'true') {
            return true;
        }

        $this->error = "Error code: {$data->errorCode} <br> Error message: {$data->errorMsg}";
        return false;
    }

    public function cngeBigbagCreate($container)

    {
        $content = (new Bigbag($container))->getRequestBody();
        $data = $this->sendRequest('cnge.bigbag.create', 'CNPMS', $content);

        if ($data && $data->success === 'true') {
            $container->update([
                'unit_code' => $data->data->bigBagTrackingNumber,
                'response' => true,
                'unit_response_list' => json_encode($data)
            ]);
            return true;
        }

        $this->error = "Error code: {$data->errorCode} <br> Error message: {$data->errorMsg}";
        return false;
    }

    public function cngeCn38Request(DeliveryBill $deliveryBill)
    {
        $content = (new CN38Request($deliveryBill))->getRequestBody();
        $data = $this->sendRequest('cnge.cn38.request', 'CGOP', $content);

        if ($data && $data->success === 'true'){
            $deliveryBill->update([
                'cnd38_code' => null,
                'request_id' => 'loading...',
            ]);
            return true;
        }

        $this->error = "Error code: {$data->errorCode} <br> Error message: {$data->errorMsg}";
        return false;
    }

    public function cngeAirlineReceive($request)
    {
        $content = (new AirlineReceive($request))->getRequestBody();
        dump('need to implement cngeAirlineReceive');
        dd($content);
        $this->sendRequest('cnge.airline.receive', 'TO_CODE_HERE', $content);
    }

    public function cngeAirlineArrive($request)
    {
        $content = (new AirlineArrive($request))->getRequestBody();
        dump($content);
        dd('need to implement AirlineArrive');
        $this->sendRequest('cnge.airline.arrive', 'TO_CODE_HERE', $content);
    }
    public function unitInfo($request)
    {
        try {

            if ($request->type == 'units_arrival') {
                return  $this->cngeAirlineArrive();
            } elseif ($request->type == 'departure_cn38'){
                return  $this->cngeAirlineReceive();
            } else {
                $returnName = [
                    "units_arrival"  =>  "Units Arrival Confirmation",//already handled
                    "units_return" => "Available Units for Return",
                    "confirm_departure" => "Confirmed Departure Units",
                    "departure_info" => "Return Departure Information",
                    "departure_cn38" => "Departure Request CN38",//already handled

                ][$request->type];
                return new PackageError("Cainiao does not handle the  '$returnName'");
            }
        } catch (\Exception $exception) {

            return new PackageError($exception->getMessage());
        }
    }
    function cngeCn38CallbackWebHook($request) {
        $data = $request->all();  
        Log::info([
            'Post cainiao webhook data'=>$data
        ]);
        $jsonDecode = json_decode($data['logistics_interface']);  
        $cn38List = $jsonDecode->cn38List; 
        $ULDNoBatchNo = $jsonDecode->ULDNoBatchNo; 
        if(isset(explode('-',$ULDNoBatchNo)[1])){
            $id= explode('-',$ULDNoBatchNo)[1];
            $deliveryBills = DeliveryBill::find($id);
            $deliveryBills->update([
                'response'=>$data,
                'request_id'=>$ULDNoBatchNo,
                'cnd38_code'=>implode('',$cn38List),
            ]);
        }else{
            Log::info('ID not found in post webhook cainiao');
        }
        return response()->json(['status' => 'success post','request'=>$data]);
    }
    public function addOrderTracking($order)
    {
        if ($order->trackings->isEmpty()){
            OrderTracking::create([
                'order_id' => $order->id,
                'status_code' => Order::STATUS_PAYMENT_DONE,
                'type' => 'HD',
                'description' => 'Order Placed',
                'country' => $order->user->country->code ?? 'US',
                'city' => 'Miami',
            ]);
        }
        return true;
    }
}