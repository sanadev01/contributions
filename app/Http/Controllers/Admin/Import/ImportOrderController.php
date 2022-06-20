<?php

namespace App\Http\Controllers\Admin\Import;

use Illuminate\Http\Request;
use App\Models\ImportedOrder;
use App\Http\Controllers\Controller;
use App\Repositories\ImportOrderRepository;

class ImportOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.import-order.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,ImportOrderRepository $repository)
    {
        $repository->storeOrderAll($id);
        session()->flash('alert-success','All Order Moved successfully');
        return back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(ImportedOrder $importOrder,ImportOrderRepository $repository)
    {
        $repository->storeOrder($importOrder);
        
        if ( $repository->importedOrderDelete($importOrder) ){
            session()->flash('alert-success','Order Moved successfully');
            return back();
        }

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
    public function destroy(ImportedOrder $importOrder, ImportOrderRepository $repository)
    {
        if ( $repository->importedOrderDelete($importOrder) ){
            session()->flash('alert-success','Import Order Deleted');
            return back();
        }

        session()->flash('alert-danger','Error While Deleting Import Order');
        return back();
    }
}
