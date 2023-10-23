<?php

namespace App\Repositories\Reports;

use SoapClient;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use Illuminate\Support\Facades\Auth;
use App\Services\Correios\Services\Brazil\CorreiosTrackingService;

class KPIReportsRepository
{
    protected $error;
    protected $wsdlUrl;
    protected $user;
    protected $password;

    public function __construct()
    {
        $this->wsdlUrl = 'http://webservice.correios.com.br/service/rastro/Rastro.wsdl';
        $this->user = '9912501576';
        $this->password = 'N>WTBF@3GP';
    }

    public function get(Request $request)
    {
        $orders = Order::with('user')
        ->where('corrios_tracking_code','!=',null)->where('status', '>=', Order::STATUS_SHIPPED)
        ->whereHas('shippingService',function($orders) {
                return $orders->whereIn('service_sub_class', [
                    ShippingService::Packet_Standard, 
                    ShippingService::Packet_Express, 
                    ShippingService::AJ_Packet_Standard, 
                    ShippingService::AJ_Packet_Express, 
                    ShippingService::Prime5, 
                    ShippingService::GePS,
                    ShippingService::Post_Plus_Registered,
                    ShippingService::Post_Plus_EMS,
                    ShippingService::Post_Plus_Prime,
                    ShippingService::Post_Plus_Premium,
                    ShippingService::Prime5RIO,
                ]);
            });

        if ($request->user_id) {
            $orders->where('user_id', $request->user_id);
        }
        if (Auth::user()->isUser()) {
            $orders->where('user_id', Auth::id());
        }
        if ( $request->start_date ){
            $startDate  = $request->start_date.' 00:00:00';
            $orders->where('order_date','>=',$startDate);
        }
        if ( $request->end_date ){
            $endDate    = $request->end_date.' 23:59:59';
            $orders->where('order_date','<=',$endDate);
        }
        if ( $request->trackingNumbers ){
            $trackNos = preg_replace('/\s+/', '', $request->trackingNumbers);
            $trackNos =str_replace(',', '', $trackNos);
            $splitNos = (str_split($trackNos,13));
            $orders->whereIn('corrios_tracking_code',$splitNos);
        }

        $orders = ($orders->get());  
        $codesUsersName =  [];
        $orderDate =  [];
        foreach($orders as $order) {
            $codesUsersName[$order->corrios_tracking_code] = $order->user->name;
            $orderDate[$order->corrios_tracking_code] = $order->order_date->format('m/d/Y');
        }
        $codes = $orders->pluck('corrios_tracking_code')->toArray();

        if(empty($codes)) {
         return [
            'trackings'=>[],
            'trackingCodeUsersName'=>[],
             'orderDates' => []
         ];
        }

        $serviceClient = new CorreiosTrackingService();

        if(count($codes) > 1) {
            $response = $serviceClient->getMultiTrackings($codes);
        } elseif (count($codes) == 1) {
            $response = $serviceClient->getTracking($codes[0]);
        }
        if (isset($response->objetos) && is_array($response->objetos) && count($response->objetos) > 0) {
            
            return [
                'trackings'=> optional($response)->objetos,
                'trackingCodeUsersName'=> $codesUsersName,
                    'orderDates'=> $orderDate
            ];

            // foreach ($response->objetos as $data) {
            //     return [
            //         'trackings'=> optional($data)->eventos,
            //         'trackingCodeUsersName'=> $codesUsersName,
            //             'orderDates'=> $orderDate
            //     ];
            // }
        }

        // $client = new SoapClient($this->wsdlUrl, array('trace'=>1));
        // $request_param = array(
        //     'usuario' => $this->user,
        //     'senha' => $this->password,
        //     'tipo' => 'L',
        //     'resultado' => 'T',
        //     'lingua' => 101,
        //     'objetos' => $codes
        // );
        // $result = $client->buscaEventosLista($request_param);
        // if(!$result->return->objeto) {
        //     return false;
        // }
        // $trackings = json_decode(json_encode($result), true); ## convert the object to array (you have to)
        // if($trackings['return']['qtd'] == "1") {
        //     $trackings['return']['objeto'] = array($trackings['return']['objeto']); ## if you send only one tracking you need to add an array before the content to follow the pattern
        // }
        
    }

}
