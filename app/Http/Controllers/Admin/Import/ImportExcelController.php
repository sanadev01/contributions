<?php

namespace App\Http\Controllers\Admin\Import;

use App\Models\Order;
use App\Models\ImportOrder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\ImportOrderRepository;
use App\Services\Excel\Import\OrderImportService;

class ImportExcelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('importExcel',Order::class);
        return view('admin.import-excel.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('importExcel',Order::class);
        $user = Auth::user();
        $profitPackages = $user->profitSettings()->with('shippingService')->get(); 
        return view('admin.import-excel.create',['profitPackages'=>$profitPackages,'user'=>$user]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, ImportOrderRepository $repository)
    {
        $this->authorize('importExcel',Order::class);
        
        $this->validate($request,[
            'excel_name' => 'nullable',
            'format' => 'required',
            'excel_file' => 'required|file',
            'service_id' => 'required'
        ]);

        $response = $repository->store($request);
        
        if($response){
            return back()->withErrors(['errors' => $response]);
        }

        session()->flash('alert-success','Import Successfull');

        return redirect()->route('admin.import.import-excel.index');


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ImportOrder $importExcel)
    {
        $orders = $importExcel;
        return view('admin.import-order.index', compact('orders'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ImportOrder $importExcel, ImportOrderRepository $repository)
    {
        if ( $repository->delete($importExcel) ){
            session()->flash('alert-success','Import Order Deleted');
            return back();
        }

        session()->flash('alert-danger','Error While Deleting Import Order');
        return back();
    }
}
