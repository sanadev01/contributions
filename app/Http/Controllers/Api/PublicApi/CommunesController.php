<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Models\Commune;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CommunesController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function __invoke(Request $request)
    {
        try {
            
            $communes = Commune::select('id', 'name')->where('region_id', $request->region_id)->get();

            return apiResponse(true,'Communes Fetched',$communes);

        } catch (\Exception $ex) {
            
            return apiResponse(false,'could not Load Communes, please select region',$ex->getMessage());
        }
    }
}