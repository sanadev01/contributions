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

    public function index(Request $request)
    {
        $sort = $request->get('sort', 'group_id');
        $order = $request->get('order', 'asc');
        
        $groups = ZoneCountry::orderBy($sort, $order)
            ->get()
            ->groupBy(['group_id', 'shipping_service_id']);

        return view('admin.rates.zone-profit.index', compact('groups'));
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
            ])->where('active',true)->get();
        
        return view('admin.rates.zone-profit.create', compact('services'));
    }

    public function store(Request $request)
    {
        try{
            $file = $request->file('csv_file');
            $importService = new ImportZoneProfit($file, $request->service_id);
            $importService->handle();
            session()->flash('alert-success', 'Profit Updated Successfully');

            return  redirect()->route('admin.rates.zone-profit.index');

        }catch(Exception $exception){
            session()->flash('alert-danger','Error while Saving Group Profit: '.$exception->getMessage());
            return back();
        }
    }

    public function show($groupId, $serviceId)
    {
        $zoneProfit = ZoneCountry::where('group_id', $groupId)->where('shipping_service_id', $serviceId)->get();
        return view('admin.rates.zone-profit.show', compact('groupId', 'serviceId', 'zoneProfit'));
    }


    public function destroy($id)
    {
         ZoneCountry::where('id', $id)->delete();
         
        session()->flash('alert-success', 'Group country deleted successfully');
        return redirect()->route('admin.rates.zone-profit.index');
    }
    public function destroyZoneProfit($groupId, $serviceId)
    {
        ZoneCountry::where('group_id', $groupId)->where('shipping_service_id', $serviceId)->delete();
        session()->flash('alert-danger', 'Service Group deleted successfully!');
        return redirect()->route('admin.rates.zone-profit.index');
    }
    public function downloadZoneProfit($groupId, $serviceId)
    {
        $profitList = ZoneCountry::where('group_id', $groupId)->where('shipping_service_id', $serviceId)->get();
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
