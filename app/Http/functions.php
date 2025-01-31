<?php

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\State;
use App\Models\ShCode;
use App\Models\Country;
use App\Models\Deposit;
use App\Models\Setting;
use App\Models\ZoneRate;
use App\Models\ShippingService;
use App\Mail\User\PurchaseInsurance;
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
    $response = $data->eventos;
    for($t = count($response)-1; $t >= 0; $t--) {
        switch(optional(optional( $response)[$t])->descricao) {
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

    $endDate = date('d/m/Y');
    
    if(optional(optional($response)[0])){
        $endDate    = $response[0]->dtHrCriado;
    }

    $endDate = Carbon::createFromFormat('Y-m-d H:i:s', str_replace("T", " ", $endDate));
    $lastEvent = $endDate->format('m/d/Y');

    return [
        'delivered' => $delivered,
        'returned' => $returned,
        'taxed' => $taxed,
        'lastEvent' => $lastEvent,
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

function getGDEProfit($rates, $service)
{
    if($service == ShippingService::GDE_PRIORITY_MAIL){
        $type = 'gde_pm_profit';
    }
    if($service == ShippingService::GDE_FIRST_CLASS){
        $type = 'gde_fc_profit';
    }
    $userProfit = setting($type, null, auth()->user()->id);
    $adminProfit = setting($type, null, User::ROLE_ADMIN);
    return $profit = $userProfit ? $userProfit : $adminProfit;
}

function isActiveService($user,$shippingService){
    if($shippingService->usps_service_sub_class)
      return setting('usps', null, $user->id)? true:false;
    if($shippingService->ups_service_sub_class) 
      return setting('ups', null, $user->id)?true:false;
    if($shippingService->fedex_service_sub_class) 
      return setting('fedex', null, $user->id) ?true:false; 
    if($shippingService->gss_service_sub_class)
      return setting('gss', null, $user->id)?true:false; 
    if($shippingService->geps_service_sub_class)
      return setting('geps_service', null, $user->id)?true:false;
    if($shippingService->sweden_post_service_sub_class) 
       return setting('sweden_post', null, $user->id)?true:false; 
    return true; 
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
 
function getValidShCode($shCode, $service)
{
    $invalidShCodes = [
        '640420',
        '210610',
        '701310',
        '820559',
        '392610',
        '33051000',
        '29362990',
        '42029200',
        '33041000',
        '85442000',
        '91022900',
        '58071000',
        '64042000',
        '90041000',
        '87150000',
        '70132900',
        '39261000',
        '33079000',
        '49019900',
        '870810',
        '621010',
        '950691',
        '970600',
        '490700',
    ];
    if(optional($service)->is_total_express) {
        $type = 'Courier';
    }else {
        $type = 'Postal (Correios)';
    }

    if (in_array($shCode, $invalidShCodes)) {

        $codeLength = strlen($shCode);
    
        $searchRange = max($codeLength - 3, 0); 

        // $nearestRecord = ShCode::whereNotIn('code', $invalidShCodes)->where('type', $type)->orderByRaw('ABS(code - ' . $shCode . ')')
        // ->first();
        // if($nearestRecord) {
        //     return $nearestRecord->code;
        // }

        for ($i = $codeLength - 1; $i >= $searchRange; $i--) {
            $searchPattern = substr($shCode, 0, $i);
            $newShCode = ShCode::whereNotIn('code', $invalidShCodes)->where('code', 'like', $searchPattern . '%')->where('type', $type)->first();
            if ($newShCode) {
                return $newShCode->code;
            }
        }

    }
    return $shCode;
}

function getZoneRate($order, $service, $zoneId)
{
    $rates = ZoneRate::where(function ($query) use ($order, $service) {
        $query->where('user_id', $order->user_id)
            ->where('shipping_service_id', $service->id);
        })->orWhere(function ($query) use ($service) {
            $query->whereNull('user_id')
                ->where('shipping_service_id', $service->id);
        })->first();

    $weight = $order->getOriginalWeight();
    $decodedRates = json_decode($rates->selling_rates, true); 

    $rate = null;    
    $rateData = $rates['data'];
    
    foreach ($decodedRates as $zone => $zoneData) {

        $zoneNumber = (int) filter_var($zone, FILTER_SANITIZE_NUMBER_INT);

        if ($zoneNumber === (int)$zoneId) {
            $rateData = $zoneData;
            break;
        }
    }

    if(isset($rateData['data'])) {

        foreach ($rateData['data'] as $range => $value) {
            $rangeValue = floatval($range);
        
            $keys = array_keys($rateData['data']);
            $index = array_search($range, $keys);
            $nextWeight = isset($keys[$index + 1]) ? floatval($keys[$index + 1]) : INF;

            if ($weight >= $rangeValue && $weight < $nextWeight) {
                $rate = $value;
                break;
            }
        }
    }

    return $rate;
}

function checkParcelInsurance($data) {
    if ($data instanceof Deposit) {
        $order = Order::with('services')->find($data->order_id);
    } elseif ($data instanceof Order) {
        $order = $data;
    } else {
        \Log::error('Invalid parameter type passed to checkParcelInsurance. Expected Deposit or Order.');
    }

    if ($order) {
        foreach ($order->services as $service) {
            if (in_array($service->name, ['Insurance', 'Seguro'])) {
                try {
                    \Mail::send(new PurchaseInsurance($order));
                } catch (\Exception $ex) {
                    \Log::error('Failed to send Purchase Insurance email error: '.$ex->getMessage());
                }
            }
        }
    } else {
        \Log::warning('Order not found for Deposit ID');
    }
}
