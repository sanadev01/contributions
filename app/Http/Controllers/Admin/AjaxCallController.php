<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\State;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Services\Colombia\ColombiaPostalCodes;

use DB;

class AjaxCallController extends Controller
{
    function __invoke(Request $request)
    {
        if($request->country_id == Country::COLOMBIA) {
            $colombiaPostalCodeService = new ColombiaPostalCodes();
            $states = $colombiaPostalCodeService->getPostalData();
        }else {
            $states = State::query()->where("country_id",$request->country_id)->get(["name","code","id"]);
        }
        return response()->json($states);
    }

}
