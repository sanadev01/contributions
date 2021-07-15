<?php

namespace App\Http\Controllers\Admin\Rates;

use Exception;
use App\Models\Rate;
use App\Models\ShippingService;
use App\Http\Controllers\Controller;
use App\Repositories\RateRepository;
use App\Models\Warehouse\AccrualRate;
use App\Http\Requests\Admin\Rate\CreateRequest;
use App\Services\Excel\ImportCharges\ImportAccrualRates;

class AccrualRateController extends Controller
{   
    public function __construct()
    {
        // $this->authorizeResource(Rate::class);
    } 

    public function index()
    {
        $shippingRates = AccrualRate::all();
        return view('admin.rates.accrual-rates.index', compact('shippingRates'));
    }

    public function create()
    {   
        return view('admin.rates.accrual-rates.create');
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

    public function show($service)
    {
        return view('admin.rates.accrual-rates.show', compact('service'));
    }

}
