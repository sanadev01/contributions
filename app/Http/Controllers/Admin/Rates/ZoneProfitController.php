<?php

namespace App\Http\Controllers\Admin\Rates;

use Exception;
use App\Models\User;
use App\Models\Rate;
use App\Models\Country;
use App\Models\ZoneRate;
use App\Models\ZoneCountry;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use App\Http\Controllers\Controller;
use App\Repositories\RateRepository;
use App\Models\Warehouse\AccrualRate;
use App\Http\Requests\Admin\Rate\CreateRequest;
use App\Services\Excel\Export\ZoneProfitExport;
use App\Services\Excel\ImportCharges\ImportZoneRate;
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
        $rates = ZoneRate::orderBy('id')->get();
        return view('admin.rates.zone-profit.index', compact('groups', 'rates'));
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
            ShippingService::GSS_CEP
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

    public function addCost()
    {   
        $this->authorizeResource(Rate::class);

        $services = ShippingService::whereIn('service_sub_class', [
            ShippingService::GSS_CEP
            ])->where('active',true)->get();
        
        return view('admin.rates.zone-profit.add-cost', compact('services'));
    }

    public function uploadRates(Request $request)
    {
        try{
            $file = $request->file('csv_file');
            $importService = new ImportZoneRate($file, $request->service_id, $request->type, $request->user_id);
            $importService->handle();
            session()->flash('alert-success', 'Rates Updated Successfully');

            return  redirect()->route('admin.rates.zone-profit.index');

        }catch(Exception $exception){
            session()->flash('alert-danger','Error while Saving Rates: '.$exception->getMessage());
            return back();
        }
    }

    public function viewRates($serviceId, $zoneId, $type, $userId = null) {
        $poboxNumber = '';
        $service = ShippingService::findOrFail($serviceId);
        $ratesQuery = ZoneRate::where('shipping_service_id', $serviceId);
        
        if ($userId !== null) {
            $ratesQuery->where('user_id', $userId);
            $poboxNumber = User::where('id', $userId)->value('pobox_number');
        }
    
        $rates = $ratesQuery->first();
    
        if ($type === "cost") {
            $decodedRates = json_decode($rates->cost_rates, true); 
        } elseif ($type === "package") {
            $decodedRates = json_decode($rates->selling_rates, true); 
        }
    
        $rate = null;
    
        foreach ($decodedRates as $zone => $zoneData) {
            $zoneNumber = (int) filter_var($zone, FILTER_SANITIZE_NUMBER_INT);
    
            if ($zoneNumber === (int)$zoneId) {
                $rate = $zoneData;
                break;
            }
        }
    
        return view('admin.rates.zone-profit.view-rates', compact('service', 'rate', 'type', 'zoneId', 'poboxNumber'));
    }       

}
