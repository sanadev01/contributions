<?php

namespace App\Http\Controllers\Api\PublicApi;

use Illuminate\Http\Request;
use App\Facades\CorreosChileFacade;
use App\Http\Controllers\Controller;

class CorreiosChileNormalizeAddressController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function __invoke(Request $request)
    {
        if (!$request->filled('coummne') || !$request->filled('direction')) {
            return apiResponse(false, 'Missing required fields', null);
        }

        $response = CorreosChileFacade::validateAddress($request->coummne, $request->direction);
        
        if ($response['success'] == true) {
            return apiResponse(true, 'Address Fetched', $response['data']);
        }

        return apiResponse(false, $response['message'], null);
    }
}