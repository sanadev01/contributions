<?php

namespace App\Http\Controllers\Api\publicApi;

use App\Http\Controllers\Controller;
use App\Models\HandlingService;

class InsuranceController extends Controller
{
    public $trackings;

    public function __invoke()
    {
        return response()->json(HandlingService::query()->active()->get());
    }
}
