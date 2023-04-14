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
    $id = str_split(trim($warehouseNumer));  
    switch(strlen($warehouseNumer)-4){
        case (1):{ 
            $whrNo = $id[0];
            break;
        }case (2):{ 
            $whrNo = $id[0].$id[1];
            break;
        }case (3):{ 
            $whrNo = $id[0].$id[1].$id[2];
            break;
        }
        case (4):{  
            $whrNo = $id[0].$id[1].$id[2].$id[5];
            break;
        }case (5):{
            $whrNo = $id[0].$id[1].$id[2].$id[5].$id[6];
            break;
        }case (6):{ 
            $whrNo = $id[0].$id[1].$id[2].$id[5].$id[6].$id[7];
            break;
        }
        case (7):{  
            $whrNo = $id[0].$id[1].$id[2].$id[5].$id[6].$id[7].$id[10];
            
            break;
        }case (8):{
            $whrNo = $id[0].$id[1].$id[2].$id[5].$id[6].$id[7].$id[10].$id[11];
            
            break;
        }case (9):{ 
            $whrNo = $id[0].$id[1].$id[2].$id[5].$id[6].$id[7].$id[10].$id[12].$id[12];
            break;
        }

        case (10):{
            $whrNo = $id[0].$id[1].$id[2].$id[5].$id[6].$id[7].$id[8].$id[9].$id[10].$id[13];
            
            break;
        }case (11):{
            $whrNo = $id[0].$id[1].$id[2].$id[5].$id[6].$id[7].$id[8].$id[9].$id[10].$id[13].$id[14];
            break;
        }case (12):{ 
            $whrNo = $id[0].$id[1].$id[2].$id[5].$id[6].$id[7].$id[8].$id[9].$id[10].$id[13].$id[14].$id[15];
            break;
        }
        default:
         abort(404);
         
    } 
    return (int) $whrNo;
}