<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Orders\PreAlertCreateRequest;
use App\Http\Requests\Orders\PreAlertUpdateRequest;
use App\Models\Order;
use App\Repositories\PreAlertRepository;
use Illuminate\Http\Request;

class PreAlertController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.parcels.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', Order::class);

        return view('admin.parcels.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PreAlertCreateRequest $request, PreAlertRepository $preAlertRepository)
    {
        $this->authorize('create', Order::class);

        if ( $preAlertRepository->store($request) ){
            session()->flash('alert-success','parcel.Parcel Added');
            return redirect()->route('admin.parcels.index');
        }

        session()->flash('alert-danger','parcel.Error Parcel Create');
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
    public function edit(Order $parcel)
    {
        $this->authorize('update', $parcel);
        return view('admin.parcels.edit',compact('parcel'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PreAlertUpdateRequest $request, Order $parcel, PreAlertRepository $preAlertRepository)
    {
        $this->authorize('update', $parcel);
        $order = $preAlertRepository->update($request, $parcel);
        dd($order);
        if ( $order = $preAlertRepository->update($request, $parcel)){
            session()->flash('alert-success','parcel.Parcel Updated');
           if ($order->status == Order::STATUS_PREALERT_READY) {
               return redirect()->route('admin.parcels.index');
            } else {
                return redirect()->route('admin.orders.index');
            }
        }
        session()->flash('alert-danger','parcel.Error Parcel Update');
        return back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $parcel, PreAlertRepository $preAlertRepository)
    {
        $this->authorize('delete',$parcel);

        if ( $preAlertRepository->delete($parcel) ){
            session()->flash('alert-success','parcel.Parcel Deleted');
            return back();
        }

        session()->flash('alert-danger','parcel.Error While Deleting Parcel');
        return back();
        
    }
}
