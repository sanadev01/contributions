<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class StateController extends Controller
{
    public function __invoke(Country $country)
    {
        return $country->states()->get(['id','name','code']);
    }
}
