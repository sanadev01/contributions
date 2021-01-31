<?php

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use App\Services\Excel\Import\LeveOrderImportService;
use Illuminate\Http\Request;

class LeveOrderImportController extends Controller
{
    public function index()
    {
        return view('admin.orders.leve.import');
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            'excel_file' => 'required|file'
        ]);

        $importExcelService = new LeveOrderImportService($request->file('excel_file'),\Auth::id());
        $importExcelService->handle();

        session()->flash('alert-success','Import Successfullt');
        return redirect()->route('admin.orders.index');
    }
}
