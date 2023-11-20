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
        $zones = ZoneCountry::all();
        return view('admin.rates.zone-profit.index', compact('zones'));
    }

    public function create()
    {   
        $this->authorizeResource(Rate::class);

        return view('admin.rates.zone-profit.create');
    }

    public function store(Request $request)
    {
        try{
            $file = $request->file('csv_file');
            $importService = new ImportZoneProfit($file, $request->zone_id);
            $importService->handle();
            session()->flash('alert-success', 'Zone Profit Updated Successfully');

            return  redirect()->route('admin.rates.zone-profit.index');

        }catch(Exception $exception){
            session()->flash('alert-danger','Error while Saving Zone Profit: '.$exception->getMessage());
            return back();
        }
    }

    public function show($id)
    {
        $zoneProfit = ZoneCountry::where('zone_id', $id)->get();
        return view('admin.rates.zone-profit.show', compact('id', 'zoneProfit'));
    }

    public function destroy(ZoneCountry $id)
    {
        $zone = ZoneCountry::where('zone_id', $id)->delete();
        
        session()->flash('alert-success', 'Zone Profit Deleted');
        return redirect()->route('admin.rates.zone-profit.index');
    }
    
    public function downloadZoneProfit($id)
    {
        // dd($id, "here");
        $profitList = ZoneCountry::where('zone_id', $id)->get();
        $exportService = new ZoneProfitExport($profitList);
        return $exportService->handle();
    }

}
