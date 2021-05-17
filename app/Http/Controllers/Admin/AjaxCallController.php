<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\State;
use Illuminate\Http\Request;
use DB;

class AjaxCallController extends Controller
{
    function __invoke(Request $request)
    {
        $states = State::query()->where("country_id",$request->country_id)->get(["name","code","id"]);
        return response()->json($states);
    }

}
