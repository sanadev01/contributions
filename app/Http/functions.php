<?php

use App\Models\Order;
use App\Models\Country;
use App\Models\State;
use App\Models\Setting;
use App\Services\Calculators\AbstractRateCalculator;

function countries()
{
    $countries =  Country::all();
    return $countries;
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