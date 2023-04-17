<?php

use Carbon\Carbon;
use App\Models\Order;
use App\Models\State;
use App\Models\Country;
use App\Models\Deposit;
use App\Models\Setting;
use App\Models\ShippingService;
use App\Models\User;
use App\Services\Calculators\AbstractRateCalculator;

function countries()
{
    $countries =  Country::all();
    return $countries;
}

function shippingServices($includeInactive=false)
{
    $query = ShippingService::query();
    if ( !$includeInactive ){
        $query->active();
    }

    return $query->get();
}

function states($countryId=null){
    if ( $countryId ){
        return State::where('country_id',$countryId)->get();
    }
    $states =  State::all();
    return $states;
}

function saveSetting($key, $value, $userId = null, $admin = false)
{
    if (! $userId && ! $admin) {
        $userId = auth()->user()->isUser() ? auth()->id() : null;
    }

    return Setting::saveByKey($key, $value, $userId);
}

function setting($key, $default = null, $userId = null, $admin = false)
{
    if (! $userId && ! $admin) {
        $userId = auth()->user()->isUser() ? auth()->id() : null;
    }

    return Setting::getByKey($key, $default, $userId);
}

function cleanString($string)
{
    // allow only letters and numbers
    $res = preg_replace("/[^a-zA-Z0-9]/", "", $string);
    return $res;
}


function __default($value,$default)
{
    return $value ? $value : $default;
}

function apiResponse($success,$message,$data=null){
    return response()->json([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
}

function generateRandomString($length = 30)
{
    if ( ! function_exists('openssl_random_pseudo_bytes'))
    {
        throw new RuntimeException('OpenSSL extension is required.');
    }
    $bytes = openssl_random_pseudo_bytes($length * 2);
    if ($bytes === false)
    {
        throw new RuntimeException('Unable to generate random string.');
    }
    return substr(str_replace(array('/', '+', '='), '', base64_encode($bytes)), 0, $length);
}

function getBalance($user = null)
{
    return Deposit::getCurrentBalance($user);
}

function chargeAmount($amount,$order=null,$description=null)
{
    return Deposit::chargeAmount($amount,$order,$description);
}

function setUSCosts($api_cost, $profit_cost)
{
    return [
        'api_cost' => $api_cost,
        'profit_cost' => $profit_cost
    ];
}

function getTotalBalance()
{
    return Deposit::getLiabilityBalance();
}

function sortTrackingEvents($data, $report)
{
    $delivered = "No";
    $returned = "No";
    $taxed = "No";
    $response = $data['evento'];
    for($t = count($response)-1; $t >= 0; $t--) {
        switch(optional(optional( $response)[$t])['descricao']) {
            case "Objeto entregue ao destinatário":
                $delivered = "Yes";
                if($taxed == "")
                    $taxed = "No";
            break;
            case "Devolução autorizada pela Receita Federal":
            case "A entrada do objeto no Brasil não foi autorizada pelos órgãos fiscalizadores":
                $returned = "Yes";
            break;
            case "Aguardando pagamento":
            case "Pagamento confirmado":
                $taxed = "Yes";
            break;
            case "Fiscalização aduaneira finalizada":
                if($taxed == "")
                    $taxed = "No";
            break;
        }
    }

    $eventsQtd = count($response)-1;
    $startDate = date('d/m/Y');
    $endDate = date('d/m/Y');
    if(optional(optional($response)[$eventsQtd])['data'] && optional(optional($response)[0])['data']){
        $startDate  = optional(optional($response)[$eventsQtd])['data'];
        $endDate    = optional(optional($response)[0])['data'];
    }
    
    $firstEvent = Carbon::parse(Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d'));
    $lastEvent = Carbon::parse(Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d'));

    if($firstEvent && $lastEvent){
        $interval = $firstEvent->diffInDays($lastEvent).' days';
    }else {
        $interval = "0 days";
    }

    return [
        'delivered' => $delivered,
        'returned' => $returned,
        'taxed' => $taxed,
        'diffDates' => $interval,
    ];
}

function getAutoChargeData(User $user)
{
    return[
        'status' => old('charge') ?? setting('charge', null, $user->id)?'Active':'Inactive',
        'card'  => "**** **** **** ". substr(optional($user->billingInformations->where('id',setting('charge_biling_information', null,auth()->id()))->first())->card_no??"****" ,-4),
        'amount' =>  setting('charge_amount', null, $user->id),
        'limit' => setting('charge_limit', null, $user->id),
    ];
}
function orignalWarehouseNumber($warehouseNumer)
{ 
    $arr = explode("-", $warehouseNumer);
    if(count($arr)>1){
        $warehouseNumer = $arr[1];
    }
    $warehouseNumer = trim($warehouseNumer);
    $whrLen = strlen($warehouseNumer)-4;

    $_1To3 = substr($warehouseNumer,0,3); 
    $_5To7 = substr($warehouseNumer,5,3); 
    $_8To10 = substr($warehouseNumer,8,3);
 

    switch(true){
        case ($whrLen == 1 || $whrLen == 2 ||$whrLen == 3):{
            $whrNo = substr($warehouseNumer,0,$whrLen);
            break;
        } 
        case ($whrLen == 4 || $whrLen == 5 ||$whrLen == 6):{
             $whrNo = $_1To3.substr($warehouseNumer,5,$whrLen-3);
            break;
        }
        case ($whrLen == 7 ||$whrLen == 8 ||$whrLen == 9 ):{
            $whrNo = $_1To3.$_5To7.substr($warehouseNumer,10,3);;
            break;
        }
        case ($whrLen == 10 ||$whrLen == 11 ||$whrLen == 12 ):{
            $whrNo = $_1To3.$_5To7.$_8To10.substr($warehouseNumer,13,3);
            break;
        }
        default:
        $whrNo = -1;
        
    }  

    dd($whrNo);
    return (int) $whrNo;
}