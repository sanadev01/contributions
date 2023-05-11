<?php

namespace App\Http\Controllers\Admin\Consolidation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Consolidation\CreateRequest;
use App\Models\Order;
use App\Models\Ticket;
use App\Repositories\PreAlertRepository;
use Illuminate\Http\Request;

class SelectPackagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PreAlertRepository $preAlertRepository)
    {
        $this->authorize('consolidateParcel',Order::class);
        return view('admin.consolidation.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRequest $request,PreAlertRepository $preAlertRepository)
    {
        $this->authorize('consolidateParcel',Order::class);
        $consolidatedOrder = $preAlertRepository->createConsolidationRequest($request);
        if ( $consolidatedOrder ){
            session()->flash('alert-success',__('consolidation.success_message_created'));
            return \redirect()->route('admin.consolidation.parcels.services.index',$consolidatedOrder);
        }

        session()->flash('alert-danger',__('consolidation.error_message_created',['error'=> $preAlertRepository->getError()]));
        return back()->withInput();
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $parcel)
    {
        $this->authorize('updateConsolidation',$parcel);

        return view('admin.consolidation.edit',compact('parcel'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $parcel, PreAlertRepository $preAlertRepository)
    {
        $this->authorize('updateConsolidation',$parcel);
        
        $consolidatedOrder = $preAlertRepository->updateConsolidationRequest($request,$parcel);
        if ( $consolidatedOrder ){
            session()->flash('alert-success',__('consolidation.success_message_updated'));
            return \redirect()->route('admin.consolidation.parcels.services.index',$consolidatedOrder);
        }

        session()->flash('alert-danger',__('consolidation.error_message_updated',['error'=> $preAlertRepository->getError()]));
        return back()->withInput();
    }

}
