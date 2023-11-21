<?php

namespace App\Http\Controllers\Admin\Rates;

use Exception;
use App\Models\Rate;
use App\Models\Country;
use App\Models\ZoneCountry;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use App\Http\Controllers\Controller;
use App\Repositories\RateRepository;
use App\Models\Warehouse\AccrualRate;
use App\Http\Requests\Admin\Rate\CreateRequest;
use App\Services\Excel\Export\ZoneProfitExport;
use App\Services\Excel\ImportCharges\ImportZoneProfit;

class ZoneProfitController extends Controller
{   
    public function __construct()
    {
        
    } 

    public function index()
    {
        $this->authorizeResource(Rate::class);

        $zones = ZoneCountry::orderBy('zone_id')
            ->orderBy('shipping_service_id')
            ->get()
            ->groupBy(['zone_id', 'shipping_service_id']);

        return view('admin.rates.zone-profit.index', compact('zones'));
    }

    public function create()
    {   
        $this->authorizeResource(Rate::class);

        $services = ShippingService::whereIn('service_sub_class', [
            ShippingService::GSS_PMI,
            ShippingService::GSS_EPMEI, 
            ShippingService::GSS_EPMI, 
            ShippingService::GSS_FCM, 
            ShippingService::GSS_EMS, 
            ])->get();
        
        return view('admin.rates.zone-profit.create', compact('services'));
    }

    public function store(Request $request)
    {
        try{
            $file = $request->file('csv_file');
            $importService = new ImportZoneProfit($file, $request->zone_id, $request->service_id);
            $importService->handle();
            session()->flash('alert-success', 'Zone Profit Updated Successfully');

            return  redirect()->route('admin.rates.zone-profit.index');

        }catch(Exception $exception){
            session()->flash('alert-danger','Error while Saving Zone Profit: '.$exception->getMessage());
            return back();
        }
    }

    public function show($zoneId, $serviceId)
    {
        $zoneProfit = ZoneCountry::where('zone_id', $zoneId)->where('shipping_service_id', $serviceId)->get();
        return view('admin.rates.zone-profit.show', compact('zoneId', 'serviceId', 'zoneProfit'));
    }


    public function destroy(ZoneCountry $id)
    {
        $zone = ZoneCountry::where('zone_id', $id)->delete();
        
        session()->flash('alert-success', 'Zone Profit Deleted');
        return redirect()->route('admin.rates.zone-profit.index');
    }
    
    public function downloadZoneProfit($zoneId, $serviceId)
    {
        $profitList = ZoneCountry::where('zone_id', $zoneId)->where('shipping_service_id', $serviceId)->get();
        $exportService = new ZoneProfitExport($profitList);
        return $exportService->handle();
    }

    public function updateZoneProfit(Request $request, $id)
    {
        $country = ZoneCountry::find($request->data_id);
        if($country) {
            $country->update(['profit_percentage' => $request->profit]);
            session()->flash('alert-success', 'Profit Updated');
            return back();
        }
    }

}
