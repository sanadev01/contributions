<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Models\Order;
use App\Models\State;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Api\DomesticRateRepository;

class DomesticLabelRateController extends Controller
{
    public $request;
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, DomesticRateRepository $domesticRateRepository)
    {
        return $domesticRateRepository->domesticServicesRates($request);
        
    }
}
