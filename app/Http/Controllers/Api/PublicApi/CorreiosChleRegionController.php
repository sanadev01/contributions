<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Facades\CorreosChileFacade;
use App\Http\Controllers\Controller;

class CorreiosChleRegionController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function __invoke()
    {
        $response = CorreosChileFacade::getAllRegions();

        if ($response['success'] == true) {

            return apiResponse(true, 'Regions Fetched', $response['data']);

        }

        return apiResponse(false, 'could not Load Regions plaease reload', null);
    }
}