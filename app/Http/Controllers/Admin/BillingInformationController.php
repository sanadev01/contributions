<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BillingInformation\CreateRequest;
use App\Http\Requests\Admin\BillingInformation\UpdateRequest;
use App\Repositories\BillingInformationRepository;
use Illuminate\Http\Request;
use App\Models\BillingInformation;

class BillingInformationController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(BillingInformation::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @param BillingInformationRepository $repository
     * @return \Illuminate\Http\Response
     */
    public function index(BillingInformationRepository $repository)
    {
        $billingInformation = $repository->get();
        return view('admin.billing-information.index',compact('billingInformation'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.billing-information.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateRequest $request
     * @param BillingInformationRepository $repository
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRequest $request, BillingInformationRepository $repository)
    {
        if ( $repository->store($request) ){
            session()->flash('alert-success','Billing Information Saved Successfully');
            return redirect()->route('admin.billing-information.index');
        }

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
     * @param BillingInformation $billingInformation
     * @return \Illuminate\Http\Response
     */
    public function edit(BillingInformation $billingInformation)
    {
        return view('admin.billing-information.edit',compact('billingInformation'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param BillingInformation $billingInformation
     * @param BillingInformationRepository $repository
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, BillingInformation $billingInformation, BillingInformationRepository $repository)
    {
        if ( $repository->update($request,$billingInformation) ){
            session()->flash('alert-success','Billing Information Updated Successfully');
            return redirect()->route('admin.billing-information.index');
        }

        return back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param BillingInformation $billingInformation
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(BillingInformation $billingInformation)
    {
        $billingInformation->delete();

        session()->flash('alert-success','Billing Information Deleted Successfully');

        return back();
    }
}
