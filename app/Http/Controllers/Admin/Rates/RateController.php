<?php

namespace App\Http\Controllers\Admin\Rates;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rate;
use App\Models\Country;
use App\Models\ShippingService;
use App\Services\Excel\ImportCharges\ImportBPSCharges;

class RateController extends Controller
{
    public function index()
    {
        $rates = Rate::first() ?? new Rate;
        return view('admin.rates.bps-leve.index', compact('rates'));
    }

    public function create()
    {   
        $countries = Country::all();
        $shipping_services = ShippingService::all();
        return view('admin.rates.bps-leve.create', compact('countries', 'shipping_services'));
    }

    public function store(Request $request)
    {
        $rules = [
            'csv_file' => 'required|file|max:15000|mimes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,xlsx',
        ];

        $this->validate($request, $rules, [
            'csv_file.required' => 'An Excel File less then 15Mb is required',
            'csv_file.max' => 'An Excel File less then 15Mb is allowed',
            'csv_file.mimes' => 'A Valid Excel File is allowed'
        ]);

        $file = $request->file('csv_file');

        try {
            $importService = new ImportBPSCharges($file);
            $importService->handle();
            session()->flash('alert-success', 'Rates Updated Successfully');
        } catch (\Exception $exception) {
            throw $exception;
            session()->flash('alert-danger', 'Error While Updating Rates');
            return back();
        }

        return  redirect()->route('admin.rates.bps-leve.index');
    }

}
