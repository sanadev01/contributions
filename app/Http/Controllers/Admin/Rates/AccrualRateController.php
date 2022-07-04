<?php

namespace App\Http\Controllers\Admin\Rates;

use Exception;
use App\Models\Rate;
use App\Models\Country;
use App\Models\ShippingService;
use App\Http\Controllers\Controller;
use App\Repositories\RateRepository;
use App\Models\Warehouse\AccrualRate;
use App\Http\Requests\Admin\Rate\CreateRequest;
use App\Services\Excel\Export\AccuralRateExport;
use App\Services\Excel\ImportCharges\ImportAccrualRates;

class AccrualRateController extends Controller
{   
    public function __construct()
    {
        
    } 

    public function index()
    {
        $this->authorizeResource(Rate::class);
        
        return view('admin.rates.accrual-rates.index');
    }

    public function create()
    {   
        $this->authorizeResource(Rate::class);
        $countryChile = Country::Chile;
        return view('admin.rates.accrual-rates.create', compact('countryChile'));
    }

    public function store(CreateRequest $request)
    {
        try{

            $file = $request->file('csv_file');
            $importService = new ImportAccrualRates($file, $request->service_id, $request->country_id);
            $importService->handle();
            session()->flash('alert-success', 'Accrual Rates Updated Successfully');

            return  redirect()->route('admin.rates.accrual-rates.index');

        }catch(Exception $exception){
            session()->flash('alert-danger','Error while Saving Rate: '.$exception->getMessage());
            return back();
        }
    }

    public function showRates($service)
    {
        return view('admin.rates.accrual-rates.show', compact('service'));
    }
    
    public function downloadRates($service)
    {
       $rates = AccrualRate::where('service', $service)->get();
       $exportService = new AccuralRateExport($rates);
        return $exportService->handle();
    }

}
