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
            $created_at = array_reverse($order->trackings->toArray())[0]['created_at'];
            $firstEventDate[$order->corrios_tracking_code] = date('m/d/Y', strtotime($created_at));
            $codesUsersName[$order->corrios_tracking_code] = $order->user->name;
            $orderDate[$order->corrios_tracking_code] = $order->order_date->format('m/d/Y');
        }
        $codes = $orders->pluck('corrios_tracking_code')->toArray();

        if(empty($codes)) {
         return [
            'trackings'=>[],
            'firstEventDate'=>[],
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
                'firstEventDate'=> $firstEventDate,
                'trackingCodeUsersName'=> $codesUsersName,
                'orderDates'=> $orderDate
            ];
        }
        
    }

}
