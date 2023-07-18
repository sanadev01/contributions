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

function responseUnprocessable($message)
{
    return response()->json([
        'success' => false,
        'message' => $message,
    ], 422);
}
function responseSuccessful($output, $message)
{
    return response()->json([
        'success' => true,
        'output' => $output,
        'message' =>  $message,
    ]);
}
function orderProductsValue($products)
{
    return array_reduce($products,function($count,$product){
           return  $count + ($product['value'])*($product['quantity']);
    });
}

function getUSAZone($state)
{
    if($state == 'FL') {
        return 'Z3';
    }elseif(in_array($state, ['AL', 'GA', 'SC'])) {
        return 'Z4';
    }elseif(in_array($state, ['LA','AR', 'MS', 'TN', 'NC', 'KY', 'VA', 'DE', 'MD', 'OH', 'NJ', 'PA'])) {
        return 'Z5';
    }elseif(in_array($state, ['TX', 'OK', 'KS', 'NE', 'MO', 'IA', 'IL', 'WI', 'NY', 'CT', 'RI', 'VT', 'NH', 'ME', 'MA', 'MI'])) {
        return 'Z6';
    }elseif(in_array($state, ['NM', 'CO', 'SD', 'ND', 'MN'])) {
        return 'Z7';
    }elseif(in_array($state, ['AZ', 'UT', 'WY', 'MT', 'ID', 'NV', 'OR', 'WA', 'CA', 'AK', 'HI'])) {
        return 'Z8';
    }
}

function getJsonData($rates, $profit)
{
    $ratesArray = [];
    foreach ($rates as $rate) {
        $ratesArray[] = [
            'weight' => optional($rate)['weight'],
            'leve' => number_format(($profit / 100) * $rate['leve'] + $rate['leve'], 2),
        ];
    }
    return json_encode($ratesArray);
}
function getFileTypeByBase64($base64)
    {
        $data = substr($base64,0,5);
        switch (strtoupper($data))
        {
            case "IVBOR":
                return "png";
            case "/9J/4":
                return "jpg";
            case "AAAAI":
                return "mp4";
            case "JVBER":
                return "pdf";
            case "AAABA":
                return "ico";
            case "UMFYI":
                return "rar";
            case "E1XYD":
                return "rtf";
            case "U1PKC":
                return "txt"; 
            case "SUQzA":
                return "mp3";
            case "77U/M":
                return "srt";
            default:
                return null;
        }
    }