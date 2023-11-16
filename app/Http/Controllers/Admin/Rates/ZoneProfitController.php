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
use App\Services\Excel\Export\AccuralRateExport;
use App\Services\Excel\ImportCharges\ImportZoneProfit;

class ZoneProfitController extends Controller
{   
    public function __construct()
    {
        
    } 

    public function index()
    {
        $this->authorizeResource(Rate::class);
        
        return view('admin.rates.zone-profit.index');
    }

    public function create()
    {   
        $this->authorizeResource(Rate::class);

        return view('admin.rates.zone-profit.create');
    }

    public function store(Request $request)
    {
        \Log::info('request');
        \Log::info($request->all());

        // dd($request->all());
        try{
            // dd("here");
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

    public function show($service)
    {
        return view('admin.rates.zone-profit.show', compact('service'));
    }

    public function destroy(ZoneCountry $id)
    {
        $zone = ZoneCountry::where('zone_id', $id)->delete();
        
        session()->flash('alert-success', 'Zone Profit Deleted');
        return redirect()->route('admin.rates.zone-profit.index');
    }
    
    public function downloadRates($service)
    {
       $rates = AccrualRate::where('service', $service)->get();
       $exportService = new AccuralRateExport($rates);
        return $exportService->handle();
    }

}
