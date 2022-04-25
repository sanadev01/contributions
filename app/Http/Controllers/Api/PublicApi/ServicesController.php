<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Http\Controllers\Controller;
use App\Models\ShippingService;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    public function __invoke($countryCode = null)
    {

        if ($countryCode) {
            
            if (strtoupper($countryCode) != 'BR' && strtoupper($countryCode) != 'US') {
                
                return response()->json([
                    'data' => null,
                    'error' => 'no shipping service found against selected country',
                ], 400);
            }

            if (strtoupper($countryCode) == 'BR') {

                $shippingServices = ShippingService::whereIn('service_sub_class', $this->correosShippingServices())->get();
            
            }elseif (strtoupper($countryCode) == 'US') {

                $shippingServices = ShippingService::whereIn('service_sub_class', $this->usShippingServices())->get();
            }
            
            return response()->json([
                'data' => $shippingServices,
                'error' => null,
            ], 200);
        }

        $correiosShippingServices = ShippingService::active()->has('rates')->get()->map(function($service){
            return collect($service->toArray())->except([ 'active','created_at', 'updated_at'])->all();
        });
        
        $usShippingServices = ShippingService::active()->whereIn('service_sub_class', $this->usShippingServices())
                                                        ->orWhere('service_sub_class', ShippingService::USPS_PRIORITY_INTERNATIONAL)
                                                        ->orWhere('service_sub_class', ShippingService::USPS_FIRSTCLASS_INTERNATIONAL)
                                                        ->get()->map(function($service){
            return collect($service->toArray())->except([ 'active','created_at', 'updated_at'])->all();
        });
        
        $shippingServices = $correiosShippingServices->merge($usShippingServices);
        return response()->json([
            'data' => $shippingServices,
            'error' => null,
        ], 200);
    }

    private function usShippingServices()
    {
        return [
            ShippingService::USPS_PRIORITY, 
            ShippingService::USPS_FIRSTCLASS,
            ShippingService::UPS_GROUND, 
            ShippingService::FEDEX_GROUND
        ];
    }

    private function correosShippingServices()
    {
        return [
            ShippingService::Packet_Standard, 
            ShippingService::Packet_Express, 
            ShippingService::Packet_Mini,
            ShippingService::USPS_PRIORITY_INTERNATIONAL,
            ShippingService::USPS_FIRSTCLASS_INTERNATIONAL,
        ];
    }
}
