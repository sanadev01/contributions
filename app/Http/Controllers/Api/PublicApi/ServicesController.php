<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Http\Controllers\Controller;
use App\Models\ShippingService;
use Illuminate\Http\Request;
use App\Models\User;

class ServicesController extends Controller
{
    private $adminId;

    public function __construct()
    {
        $this->adminId = User::ROLE_ADMIN;
    }

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

        if ($correiosShippingServices->isNotEmpty()) {
            $correiosShippingServices = $this->filterCorreiosServices($correiosShippingServices);
        }
        
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
            ShippingService::FEDEX_GROUND,
            ShippingService::GSS_PMI,
            ShippingService::GSS_EPMEI,
            ShippingService::GSS_EPMI,
            ShippingService::GSS_FCM,
            ShippingService::GSS_EMS,
        ];
    }

    private function correosShippingServices()
    {
        if(!setting('anjun_api', null, $this->adminId) || !setting('bcn_api', null, $this->adminId)){
            $correiosServices =  [
                ShippingService::Packet_Standard, 
                ShippingService::Packet_Express, 
                ShippingService::Packet_Mini,
            ];
        }

        if(setting('anjun_api', null, $this->adminId) || setting('bcn_api', null, $this->adminId)){
            $correiosServices =  [
                ShippingService::AJ_Packet_Standard, 
                ShippingService::AJ_Packet_Express,
            ];
        }

        array_push($correiosServices, ShippingService::USPS_PRIORITY_INTERNATIONAL, ShippingService::USPS_FIRSTCLASS_INTERNATIONAL,);

        return $correiosServices;
    }

    private function filterCorreiosServices($correiosServices)
    {        
        if(setting('anjun_api', null, $this->adminId) || setting('bcn_api', null, $this->adminId)){
            $correiosServices = $correiosServices->filter(function ($shippingService, $key) {
                return $shippingService['service_sub_class'] != ShippingService::Packet_Standard 
                    && $shippingService['service_sub_class'] != ShippingService::Packet_Express
                    && $shippingService['service_sub_class'] != ShippingService::Packet_Mini;
            });
        }

        if(!setting('anjun_api', null, $this->adminId) || !setting('bcn_api', null, $this->adminId)){
            $correiosServices = $correiosServices->filter(function ($shippingService, $key) {
                return $shippingService['service_sub_class'] != ShippingService::AJ_Packet_Standard 
                    && $shippingService['service_sub_class'] != ShippingService::AJ_Packet_Express;
            });
        }

        return $correiosServices;
    }
    
}
