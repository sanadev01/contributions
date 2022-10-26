<?php

namespace App\Http\Controllers\Admin\Tax;

use App\Models\Tax;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Repositories\TaxRepository;
use App\Http\Controllers\Controller;


class TaxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(TaxRepository $repository, Request $request)
    {
        $taxes = $repository->get($request);
        return view('admin.tax.index', compact('taxes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(TaxRepository $repository, Request $request)
    {
        $orders = null;
        if($request->trackingNumbers) {
            $orders = $repository->getOrders($request);
        }
        return view('admin.tax.create', compact('orders'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaxRepository $repository, Request $request)
    {
        if ($repository->store($request) ){
            session()->flash('alert-success', 'Tax has been added successfully');
            return  redirect()->route('admin.tax.index');
        }
        session()->flash('alert-danger', 'Please recharge your account first!');
        return back()->withInput();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Tax $tax)
    {
        return view('admin.tax.edit',compact('tax'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tax $tax, TaxRepository $repository)
    {
        if ( $repository->update($request, $tax) ){
            session()->flash('alert-success','Tax Transaction Updated');
            return redirect()->route('admin.tax.index');
        }

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
