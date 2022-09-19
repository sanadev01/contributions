<?php

namespace App\Http\Controllers\Api\PublicApi;

use Exception;
use App\Models\Region;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RegionController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke($countryId)
    {
        try {
            $query = Region::query();

            if ($countryId == Country::COLOMBIA) {
                $query->where('country_id', $countryId)->take(1104);
            }else {
                $query->where('country_id', $countryId);
            }

            $regions = $query->get();
            
            return apiResponse(true,'Regions Fetched',$regions);

        } catch (Exception $e) {
            
            return apiResponse(false,'could not Load Regions plaease reload',$e->getMessage());
        }
    }
}
