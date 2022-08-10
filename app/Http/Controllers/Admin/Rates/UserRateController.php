<?php

namespace App\Http\Controllers\Admin\Rates;

use Illuminate\Http\Request;
use App\Models\ProfitPackage;
use App\Models\ProfitSetting;
use App\Http\Controllers\Controller;
use App\Models\ShippingService;
use App\Repositories\Reports\RateReportsRepository;

class UserRateController extends Controller
{
    public $rates;
    public $packageId;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(RateReportsRepository $rateReportsRepository)
    {
        $this->authorize('userSellingRates',ProfitPackage::class);

        $settings = ProfitSetting::where('user_id', auth()->user()->id)->get();
        if(!$settings->isEmpty())
        {
            foreach($settings as $setting)
            {
                $service = ShippingService::where('id', $setting->service_id)->first();
                if($service){
                    $rates = $rateReportsRepository->getRateReport($setting->package_id, $setting->service_id);
                    $this->rates[] = [
                        'service' => $service->name,
                        'rates' => $rates,
                        'packageId' => $setting->package_id,
                    ];
                }
            }

            $rates = $this->rates;
            $packageId = $this->packageId;
            
        } elseif(auth()->user()->package_id){
            $packageId = auth()->user()->package_id;
            $rates = $rateReportsRepository->getRateReport($packageId);

            $service = ShippingService::where('name', 'Packet Standard')->first();
            $this->rates[] = [
                'service' => $service->name,
                'rates' => $rates,
                'packageId' => $packageId,
            ];

        }else{
            
            $packageId = ProfitPackage::where('type', 'default')->first()->id; 
            $rates = $rateReportsRepository->getRateReport($packageId);
            
            $service = ShippingService::where('name', 'Packet Standard')->first();
            $this->rates[] = [
                'service' => $service->name,
                'rates' => $rates,
                'packageId' => $packageId,
            ];
        }
        $service = ShippingService::where('service_sub_class', ShippingService::Brazil_Redispatch)->first();
        if($service){
            $this->rates[] = [
                'service' => $service->name,
                'rates' => collect($service->rates[0]->data),
                'packageId' => optional(optional($service->profitPackages)[0])->id,
            ];
        }

        $rates = $this->rates;
        return view('admin.rates.profit-packages.user-profit-package.index', compact('rates'));
    }

    public function showRates(Request $request)
    {
        $rates = json_decode($request->rates, true);

        $rates = collect($rates);
        $service = $request->service;
        $packageId = $request->packageId;

        return view('admin.rates.profit-packages.user-profit-package.rates', compact('rates', 'service', 'packageId'));
    }
    
}
