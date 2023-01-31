<?php

namespace App\Repositories\Reports;

use SoapClient;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use Illuminate\Support\Facades\Auth;

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
        $orders = Order::has('user')->where('status', '>=', Order::STATUS_SHIPPED);
        $orders->whereHas('shippingService',function($orders) {
            return $orders->whereIn('service_sub_class', [
                ShippingService::Packet_Standard, 
                ShippingService::Packet_Express, 
                ShippingService::AJ_Packet_Standard, 
                ShippingService::AJ_Packet_Express, 
                ShippingService::Prime5, 
                ShippingService::GePS]);
        });
        if (Auth::user()->isUser()) {
            $orders->where('user_id', Auth::id());
        }
        $startDate  = $request->start_date.' 00:00:00';
        $endDate    = $request->end_date.' 23:59:59';
        if ( $request->start_date ){
            $orders->where('order_date','>=',$startDate);
        }
        if ( $request->end_date ){
            $orders->where('order_date','<=',$endDate);
        }

        $codes = $orders->pluck('corrios_tracking_code')->toArray();
        if(empty($codes)) {
            return $trackings = [];
        }
        $client = new SoapClient($this->wsdlUrl, array('trace'=>1));
        $request_param = array(
            'usuario' => $this->user,
            'senha' => $this->password,
            'tipo' => 'L',
            'resultado' => 'T',
            'lingua' => 101,
            'objetos' => $codes
        );
        $result = $client->buscaEventosLista($request_param);
        if(!$result->return->objeto) {
            return false;
        }
        $trackings = json_decode(json_encode($result), true); ## convert the object to array (you have to)
        if($trackings['return']['qtd'] == "1") {
            $trackings['return']['objeto'] = array($trackings['return']['objeto']); ## if you send only one tracking you need to add an array before the content to follow the pattern
        }  
        return $trackings;
    }

}
