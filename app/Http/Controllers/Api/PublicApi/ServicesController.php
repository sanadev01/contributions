<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Http\Controllers\Controller;
use App\Models\ShippingService;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    public function __invoke()
    {
        return ShippingService::active()->has('rates')->get()->map(function($service){
            return collect($service->toArray())->except([ 'active','created_at', 'updated_at'])->all();
        });
    }
}
