<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Http\Controllers\Controller;
use App\Http\Resources\PublicApi\UserShippingResource;
use App\Models\ShippingService;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\ProfitSetting;

class UserServicesController extends Controller
{

    public function __invoke()
    {
        $userId = Auth::id();
        $settings = ProfitSetting::where('user_id', $userId)->get();

        $servicesList = [];

        $addServicesToList = function ($services) use (&$servicesList) {
            $filteredServices = $this->getAssignedServices($services);
            $servicesList = array_merge($servicesList, $filteredServices->toArray());
        };

        if(setting('usps', null, $userId)) {
            $addServicesToList([
                ShippingService::USPS_PRIORITY_INTERNATIONAL,
                ShippingService::USPS_FIRSTCLASS_INTERNATIONAL,
                ShippingService::USPS_PRIORITY, 
                ShippingService::USPS_FIRSTCLASS,
            ]);            
        }

        if(setting('ups', null, $userId)) {
            $addServicesToList([
                ShippingService::UPS_GROUND,
            ]);  
        }

        if(setting('fedex', null, $userId)) {
            $addServicesToList([
                ShippingService::FEDEX_GROUND,
            ]);  
        }

        if(setting('gss', null, $userId)) {
            $addServicesToList([
                ShippingService::GSS_PMI,
                ShippingService::GSS_EMS,
                ShippingService::GSS_CEP
            ]);
        }

        if(setting('geps_service', null, $userId)) {
            $addServicesToList([
                ShippingService::GePS,
                ShippingService::GePS_EFormat,
                ShippingService::Parcel_Post,
            ]);
        }

        if(setting('sweden_post', null, $userId)) {
            $addServicesToList([
                ShippingService::Prime5,
                ShippingService::Prime5RIO,
            ]);
        }

        if(setting('post_plus', null, $userId)) {
            $addServicesToList([
                ShippingService::Post_Plus_Registered,
                ShippingService::Post_Plus_EMS,
                ShippingService::Post_Plus_Prime,
                ShippingService::Post_Plus_Premium,
            ]);
        }

        if(setting('gde', null, $userId)) {
            $addServicesToList([
                ShippingService::GDE_PRIORITY_MAIL,
                ShippingService::GDE_FIRST_CLASS,
            ]);
        }
        
        $profitPackageServices = UserShippingResource::collection($settings);
        
        $servicesList = array_merge($servicesList, $profitPackageServices->toArray(request()));

        return response()->json([
            'data' => $servicesList,
        ], 200);
    }

    private function getAssignedServices($services)
    {
        $filteredServices = ShippingService::active()
            ->whereIn('service_sub_class', $services)
            ->get()
            ->map(function ($service) {
                return collect($service->toArray())
                    ->only(['id', 'name', 'service_sub_class'])
                    ->all();
            });
        return $filteredServices;
    }
}