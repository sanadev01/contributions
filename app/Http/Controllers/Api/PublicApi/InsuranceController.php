<?php

namespace App\Http\Controllers\Api\publicApi;

use App\Http\Controllers\Controller;
use App\Models\HandlingService;

class InsuranceController extends Controller
{
    public $trackings;

    public function __invoke()
    {
        $handlingServices = HandlingService::query()
            ->active()
            ->select('id', 'name', 'price')
            ->get();

        return response()->json($handlingServices);
    }
}
