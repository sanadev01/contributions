<?php

namespace App\Http\Controllers\Admin\Rates;

use App\Models\Rate;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ProfitPackage;
use App\Models\ProfitSetting;
use App\Models\ShippingService;
use App\Http\Controllers\Controller;
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
        $gdeService = '';
        $this->authorize('userSellingRates',ProfitPackage::class);

        $settings = ProfitSetting::where('user_id', auth()->user()->id)->get();

        if(setting('gde', null, User::ROLE_ADMIN) && setting('gde', null, auth()->user()->id)){
            $gdeService = ShippingService::where('service_sub_class', ShippingService::GDE_PRIORITY_MAIL)->first();
        }
        
        $service = ShippingService::where('service_sub_class', ShippingService::Brazil_Redispatch)->first();
        
        return view('admin.rates.profit-packages.user-profit-package.index', compact('service', 'settings', 'gdeService'));
    }

    public function showPackageRates($id,$packageId)
    {
        $profit = null;
        if($packageId  == 'region'){
            $isGDE = true;
            $rates = Rate::find($id);
            if($rates && setting('gde', null, User::ROLE_ADMIN) && setting('gde', null, auth()->user()->id)){
                $type = 'gde_fc_profit';
                if($rates->shippingService->service_sub_class == ShippingService::GDE_PRIORITY_MAIL){
                    $type = 'gde_pm_profit';
                }
                $userProfit = setting($type, null, auth()->user()->id);
                $adminProfit = setting($type, null, User::ROLE_ADMIN);
                $profit = $userProfit ? $userProfit : $adminProfit;
                $service = $rates->shippingService->name;
                $rates = $rates->data;
                $packageId = $id;
                return view('admin.rates.profit-packages.user-profit-package.rates', compact('rates', 'service', 'packageId','profit', 'isGDE'));
            }

        }

        $service = ShippingService::find($id);
        
        if($service->isGDEService()){
            $shippingRegions = Rate::where('shipping_service_id', $service->id)->get();
            return view('admin.rates.profit-packages.user-profit-package.regions', compact('shippingRegions'));
        }
        
        $service = $service->name;
        $rateReportsRepository = new RateReportsRepository();
        $rates = $rateReportsRepository->getRateReport($packageId, $id);
        $isGDE = false;
        return view('admin.rates.profit-packages.user-profit-package.rates', compact('rates', 'service', 'packageId','profit', 'isGDE'));
    }
    
}
