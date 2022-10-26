<?php

namespace App\Http\Controllers\Admin\Rates;

use App\Models\Rate;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use App\Http\Controllers\Controller;
use App\Repositories\RateRepository;
use App\Http\Requests\Admin\Rate\CreateRequest;
use App\Services\Excel\Export\ShippingServiceRateExport;

class RateController extends Controller
{   
    public function __construct()
    {
        // $this->authorizeResource(Rate::class);
    } 

    public function index(RateRepository $repository)
    {
        $this->authorizeResource(Rate::class);
        $shippingRates = $repository->get();
        return view('admin.rates.shipping-rates.index', compact('shippingRates'));
    }

    public function create()
    {   
        $this->authorizeResource(Rate::class);
        $shipping_services = ShippingService::get()->filter(function($shippingService, $key){
            return !$shippingService->isOfUnitedStates();
        });
        
        return view('admin.rates.shipping-rates.create', compact('shipping_services'));
    }

    public function store(CreateRequest $request, RateRepository $repository)
    {   
        $this->authorizeResource(Rate::class);
        if ( $repository->store($request) ){
            return redirect()->route('admin.rates.shipping-rates.index');
        }

        
        // session()->flash('alert-dange','Error while importing rates');
        return back()->withInput();
    }
    
    public function show(ShippingService $shipping_rate, RateRepository $repository)
    {   
        if ($shipping_rate->service_sub_class == ShippingService::Courier_Express) {
            $defaultRate = public_path('uploads/bps/chile_regions_rate.xlsx');
            return response()->download($defaultRate);
        }

        if(optional($shipping_rate->rates)[0]){
            $rates = optional(optional($shipping_rate->rates)[0])->data;
            $exportService = new ShippingServiceRateExport($rates);
            return $exportService->handle();
        }
        $defaultRate = public_path('uploads/bps/hd-leve.xlsx');
        return response()->download($defaultRate);

    }

    public function showShippingRates($id)
    {   
        $this->authorizeResource(Rate::class);

        $shipping_rate = Rate::findorfail($id);
        return view('admin.rates.shipping-rates.show', compact('shipping_rate'));
    }

    public function downloadShippingRates($id)
    {
        $this->authorizeResource(Rate::class);
        
        $shipping_rate = Rate::findorfail($id);
        $exportService = new ShippingServiceRateExport($shipping_rate->data);
        return $exportService->handle();
    }

    public function shippingRegionRates(RateRepository $repository, ShippingService $shipping_service)
    {
        $this->authorizeResource(Rate::class);
        $shippingRegionRates = $repository->getRegionRates($shipping_service);
        
        return view('admin.rates.shipping-rates.region.index', compact('shipping_service', 'shippingRegionRates'));
    }

    public function showShippingRegionRates($id)
    {
        $this->authorizeResource(Rate::class);

        $shipping_rate = Rate::findorfail($id);
        return view('admin.rates.shipping-rates.region.show', compact('shipping_rate'));
    }

    public function postNLCountryRates(RateRepository $repository, ShippingService $shipping_service)
    {
        $this->authorizeResource(Rate::class);
        $postNLCountryRates = $repository->getPostNLCountryRates($shipping_service);
        
        return view('admin.rates.shipping-rates.country.index', compact('shipping_service', 'postNLCountryRates'));
    }
    public function showPostNLCountryRates($id)
    {
        $this->authorizeResource(Rate::class);

        $shipping_rate = Rate::findorfail($id);
        return view('admin.rates.shipping-rates.country.show', compact('shipping_rate'));
    }

}
