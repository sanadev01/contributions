<?php

namespace App\Http\Controllers\Admin\Rates;

use Illuminate\Http\Request;
use App\Models\ProfitPackage;
use App\Models\ShippingService;
use App\Http\Controllers\Controller;
use App\Repositories\ProfitPackageUploadRepository;
use App\Services\Excel\Import\ProfitPackageImportService;

class ProfitPackageUploadController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create',ProfitPackage::class);
        
        $shipping_services = ShippingService::all();
        return view('admin.rates.profit-packages.upload' ,compact('shipping_services'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        $this->authorize('create',ProfitPackage::class);
        $this->validate($request,[
            'shipping_service_id' => 'required',
            'package_name' => 'required',
            'type' => 'required',
            'file' => 'required|file'
        ]);

        $importExcelService = new ProfitPackageImportService($request->file('file'),\Auth::id(), $request);
        $importExcelService->handle();

        session()->flash('alert-success','Import Successfullt');
        return redirect()->route('admin.rates.profit-packages.index');

    }

}
