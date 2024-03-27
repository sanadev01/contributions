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
use App\Services\Calculators\AbstractRateCalculator;
use Illuminate\Support\Facades\Cache;

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
function us_states(){
    return Cache::remember('states', Carbon::now()->addDay(), function () {
        return State::query()->where('country_id', Country::US)->get(['name','code','id']);
    });
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

function getOrderGroupRange($order)
{
    if ($order) {
        $orderZipcode = str_replace('-', '', $order->recipient->zipcode);
        $groupRanges = [
            ['start' => 1000000, 'end' => 11599999, 'group' => 1],
            ['start' => 11600000, 'end' => 19999999, 'group' => 2],
            ['start' => 20000000, 'end' => 28999999, 'group' => 3],
            ['start' => 30000000, 'end' => 48999999, 'group' => 4],
            ['start' => 49100000, 'end' => 49139999, 'group' => 4],
            ['start' => 49170000, 'end' => 49199999, 'group' => 4],
            ['start' => 49220000, 'end' => 49399999, 'group' => 4],
            ['start' => 49480000, 'end' => 49499999, 'group' => 4],
            ['start' => 49512000, 'end' => 49999999, 'group' => 4],
            ['start' => 50000000, 'end' => 53689999, 'group' => 4],
            ['start' => 53700000, 'end' => 53989999, 'group' => 4],
            ['start' => 54000000, 'end' => 55119999, 'group' => 4],
            ['start' => 55190000, 'end' => 55199999, 'group' => 4],
            ['start' => 55290000, 'end' => 55304999, 'group' => 4],
            ['start' => 55600000, 'end' => 55619999, 'group' => 4],
            ['start' => 56300000, 'end' => 56354999, 'group' => 4],
            ['start' => 57130000, 'end' => 57149999, 'group' => 4],
            ['start' => 57180000, 'end' => 57199999, 'group' => 4],
            ['start' => 57210000, 'end' => 57229999, 'group' => 4],
            ['start' => 57250000, 'end' => 57264999, 'group' => 4],
            ['start' => 57270000, 'end' => 57299999, 'group' => 4],
            ['start' => 57320000, 'end' => 57479999, 'group' => 4],
            ['start' => 57490000, 'end' => 57499999, 'group' => 4],
            ['start' => 57510000, 'end' => 57599999, 'group' => 4],
            ['start' => 57615000, 'end' => 57799999, 'group' => 4],
            ['start' => 57820000, 'end' => 57839999, 'group' => 4],
            ['start' => 57860000, 'end' => 57954999, 'group' => 4],
            ['start' => 57960000, 'end' => 57999999, 'group' => 4],
            ['start' => 58115000, 'end' => 58116999, 'group' => 4],
            ['start' => 58119000, 'end' => 58199999, 'group' => 4],
            ['start' => 58208000, 'end' => 58279999, 'group' => 4],
            ['start' => 58289000, 'end' => 58299999, 'group' => 4],
            ['start' => 58315000, 'end' => 58319999, 'group' => 4],
            ['start' => 58322000, 'end' => 58336999, 'group' => 4],
            ['start' => 58338000, 'end' => 58339999, 'group' => 4],
            ['start' => 58342000, 'end' => 58347999, 'group' => 4],
            ['start' => 58347999, 'end' => 58399999, 'group' => 4],
            ['start' => 58441000, 'end' => 58442999, 'group' => 4],
            ['start' => 58450000, 'end' => 58469999, 'group' => 4],
            ['start' => 58480000, 'end' => 58499999, 'group' => 4],
            ['start' => 58510000, 'end' => 58514999, 'group' => 4],
            ['start' => 58520000, 'end' => 58699999, 'group' => 4],
            ['start' => 58710000, 'end' => 58732999, 'group' => 4],
            ['start' => 58734000, 'end' => 58799999, 'group' => 4],
            ['start' => 58815000, 'end' => 58864999, 'group' => 4],
            ['start' => 58870000, 'end' => 58883999, 'group' => 4],
            ['start' => 58887000, 'end' => 58899999, 'group' => 4],
            ['start' => 58908000, 'end' => 58918999, 'group' => 4],
            ['start' => 58920000, 'end' => 58999999, 'group' => 4],
            ['start' => 59162000, 'end' => 59279999, 'group' => 4],
            ['start' => 59310000, 'end' => 59379999, 'group' => 4],
            ['start' => 59390000, 'end' => 59569999, 'group' => 4],
            ['start' => 59575000, 'end' => 59599999, 'group' => 4],
            ['start' => 59655000, 'end' => 59699999, 'group' => 4],
            ['start' => 59730000, 'end' => 59899999, 'group' => 4],
            ['start' => 59902000, 'end' => 59999999, 'group' => 4],
            ['start' => 60000000, 'end' => 63999999, 'group' => 4],
            ['start' => 65000000, 'end' => 65034999, 'group' => 4],
            ['start' => 65080000, 'end' => 65089999, 'group' => 4],
            ['start' => 70000000, 'end' => 76799999, 'group' => 4],
            ['start' => 76799999, 'end' => 89999999, 'group' => 4],
            ['start' => 90000000, 'end' => 99999999, 'group' => 4],
            ['start' => 29000000, 'end' => 29999999, 'group' => 5],
            ['start' => 49000000, 'end' => 49099999, 'group' => 5],
            ['start' => 49140000, 'end' => 49169999, 'group' => 5],
            ['start' => 49200000, 'end' => 49219999, 'group' => 5],
            ['start' => 49400000, 'end' => 49479999, 'group' => 5],
            ['start' => 49500000, 'end' => 49511999, 'group' => 5],
            ['start' => 53690000, 'end' => 53699999, 'group' => 5],
            ['start' => 53990000, 'end' => 53999999, 'group' => 5],
            ['start' => 55120000, 'end' => 55189999, 'group' => 5],
            ['start' => 55200000, 'end' => 55289999, 'group' => 5],
            ['start' => 55305000, 'end' => 55599999, 'group' => 5],
            ['start' => 55620000, 'end' => 56299999, 'group' => 5],
            ['start' => 56355000, 'end' => 57129999, 'group' => 5],
            ['start' => 57150000, 'end' => 57179999, 'group' => 5],
            ['start' => 57200000, 'end' => 57209999, 'group' => 5],
            ['start' => 57230000, 'end' => 57249999, 'group' => 5],
            ['start' => 57265000, 'end' => 57269999, 'group' => 5],
            ['start' => 57300000, 'end' => 57319999, 'group' => 5],
            ['start' => 57480000, 'end' => 57489999, 'group' => 5],
            ['start' => 57500000, 'end' => 57509999, 'group' => 5],
            ['start' => 57600000, 'end' => 57614999, 'group' => 5],
            ['start' => 57800000, 'end' => 57819999, 'group' => 5],
            ['start' => 57840000, 'end' => 57859999, 'group' => 5],
            ['start' => 57955000, 'end' => 57959999, 'group' => 5],
            ['start' => 58000000, 'end' => 58114999, 'group' => 5],
            ['start' => 58117000, 'end' => 58118999, 'group' => 5],
            ['start' => 58200000, 'end' => 58207999, 'group' => 5],
            ['start' => 58280000, 'end' => 58288999, 'group' => 5],
            ['start' => 58300000, 'end' => 58314999, 'group' => 5],
            ['start' => 58320000, 'end' => 58321999, 'group' => 5],
            ['start' => 58337000, 'end' => 58337999, 'group' => 5],
            ['start' => 58340000, 'end' => 58341999, 'group' => 5],
            ['start' => 58348000, 'end' => 58349999, 'group' => 5],
            ['start' => 58400000, 'end' => 58440999, 'group' => 5],
            ['start' => 58443000, 'end' => 58449999, 'group' => 5],
            ['start' => 58470000, 'end' => 58479999, 'group' => 5],
            ['start' => 58500000, 'end' => 58509999, 'group' => 5],
            ['start' => 58515000, 'end' => 58519999, 'group' => 5],
            ['start' => 58700000, 'end' => 58700000, 'group' => 5],
            ['start' => 58733000, 'end' => 58733999, 'group' => 5],
            ['start' => 58800000, 'end' => 58814999, 'group' => 5],
            ['start' => 58865000, 'end' => 58869999, 'group' => 5],
            ['start' => 58884000, 'end' => 58886999, 'group' => 5],
            ['start' => 58900000, 'end' => 58907999, 'group' => 5],
            ['start' => 58919000, 'end' => 58919999, 'group' => 5],
            ['start' => 59000000, 'end' => 59161999, 'group' => 5],
            ['start' => 59280000, 'end' => 59309999, 'group' => 5],
            ['start' => 59380000, 'end' => 59389999, 'group' => 5],
            ['start' => 59570000, 'end' => 59574999, 'group' => 5],
            ['start' => 59600000, 'end' => 59654999, 'group' => 5],
            ['start' => 59700000, 'end' => 59729999, 'group' => 5],
            ['start' => 59900000, 'end' => 59901999, 'group' => 5],
            ['start' => 64000000, 'end' => 64999999, 'group' => 5],
            ['start' => 65035000, 'end' => 65079999, 'group' => 5],
            ['start' => 65090000, 'end' => 69999999, 'group' => 5],
            ['start' => 76800000, 'end' => 79999999, 'group' => 5],

            ['start' => 29166651, 'end' => 29166651, 'group' => 5],
            ['start' => 29164340, 'end' => 29164340, 'group' => 5],
            ['start' => 74230022, 'end' => 74230022, 'group' => 4],
            ['start' => 74230022, 'end' => 74230022, 'group' => 4],
            ['start' => 74603190, 'end' => 74603190, 'group' => 4],
            ['start' => 74603190, 'end' => 74603190, 'group' => 4],
            ['start' => 74150070, 'end' => 74150070, 'group' => 4],
            ['start' => 74343240, 'end' => 74343240, 'group' => 4],
            ['start' => 74280210, 'end' => 74280210, 'group' => 4],
            ['start' => 13172651, 'end' => 13172651, 'group' => 2],

        ];
            // Sort the groupRanges array based on the 'start' key
        usort($groupRanges, function ($a, $b) {
            return $a['start'] - $b['start'];
        });
        foreach ($groupRanges as $range) {
            if ($orderZipcode >= $range['start'] && $orderZipcode <= $range['end']) {
                return $range;
            } elseif ($orderZipcode < $range['start']) {
                // Break out of the loop if the current range's start is greater than the order's zipcode
                break;
            }
        }
    }
    return null;
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
    $rates = ZoneRate::where('shipping_service_id', $service->id)->first();
    $weight = $order->getWeight();
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

    return $rate;
}