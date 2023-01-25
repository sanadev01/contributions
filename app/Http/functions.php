<?php

use App\Models\Order;
use App\Models\Country;
use App\Models\Deposit;
use App\Models\State;
use App\Models\Setting;
use App\Models\ShippingService;
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

function getParcelStatus($status)
{
    if($status == Order::STATUS_PREALERT_TRANSIT) {
        $message = "STATUS_PREALERT_TRANSIT";
    }elseif($status == Order::STATUS_PREALERT_READY){
        $message = "STATUS_PREALERT_READY";
    }elseif($status == Order::STATUS_ORDER){
        $message = "STATUS_ORDER";
    }elseif($status == Order::STATUS_NEEDS_PROCESSING){
        $message = "STATUS_NEEDS_PROCESSING";
    }elseif($status == Order::STATUS_PAYMENT_PENDING){
        $message = "STATUS_PAYMENT_PENDING";
    }elseif($status == Order::STATUS_PAYMENT_DONE){
        $message = "STATUS_PAYMENT_DONE";
    }elseif($status == Order::STATUS_CANCEL) {
        $message = "STATUS_CANCEL";
    }elseif($status == Order::STATUS_REJECTED) {
        $message = "STATUS_REJECTED";
    }elseif($status == Order::STATUS_RELEASE) {
        $message = "STATUS_RELEASE";
    }elseif($status == Order::STATUS_REFUND) {
        $message = "STATUS_REFUND";
    }  

    return $message;
}

function sortTrackingEvents($data) {

    $delivered = "No"; $returned = "No"; $taxed = "No"; $diffDates = "0"; 
    for($t=count($data['evento'])-1;$t>=0;$t--) {
        switch($data['evento'][$t]['descricao']) {
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
    $eventsQtd = count($data['evento'])-1; 
    $dateFirstEvent = DateTime::createFromFormat('d/m/Y', $data['evento'][$eventsQtd]['data']); 
    $dateLastEvent = DateTime::createFromFormat('d/m/Y', $data['evento'][0]['data']);
    $interval = $dateFirstEvent->diff($dateLastEvent); 
    $diffDates = $interval->format('%R%a days');

    return [
        'delivered' => $delivered,
        'returned' => $returned,
        'taxed' => $taxed,
        'diffDates' => $diffDates,
    ];

}