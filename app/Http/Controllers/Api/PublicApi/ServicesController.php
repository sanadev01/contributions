<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Http\Controllers\Controller;
use App\Models\ShippingService;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    public function __invoke()
    {
        $correiosShippingServices = ShippingService::active()->has('rates')->get()->map(function($service){
            return collect($service->toArray())->except([ 'active','created_at', 'updated_at'])->all();
        });
        
        $usShippingServices = ShippingService::active()->where('service_sub_class', ShippingService::UPS_GROUND)
                                                        ->orWhere('service_sub_class', ShippingService::USPS_PRIORITY)
                                                        ->orWhere('service_sub_class', ShippingService::USPS_FIRSTCLASS)
                                                        ->get()->map(function($service){
            return collect($service->toArray())->except([ 'active','created_at', 'updated_at'])->all();
        });
        
        $shippingServices = $correiosShippingServices->merge($usShippingServices);
        return $shippingServices;
    }
}
