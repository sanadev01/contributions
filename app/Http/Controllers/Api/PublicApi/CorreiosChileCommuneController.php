<?php

namespace App\Http\Controllers\Api\PublicApi;

use Illuminate\Http\Request;
use App\Facades\CorreosChileFacade;
use App\Http\Controllers\Controller;

class CorreiosChileCommuneController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function __invoke(Request $request)
    {
        $response = CorreosChileFacade::getchileCommunes($request->region_code);

        if ($response['success'] == true) {

            return apiResponse(true, 'Communes Fetched', $response['data']);

        }

        return apiResponse(false, 'could not Load Communes plaease reload', null);
    }
}