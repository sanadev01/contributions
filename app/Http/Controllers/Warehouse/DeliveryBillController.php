<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Http\Requests\Warehouse\DeliveryBill\CreateDeliveryBillRequest;
use App\Models\Warehouse\DeliveryBill;
use App\Repositories\Warehouse\DeliveryBillRepository;
use Illuminate\Http\Request;

class DeliveryBillController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param DeliveryBillRepository $deliveryBillRepository
     * @return \Illuminate\Http\Response
     */
    public function index(DeliveryBillRepository $deliveryBillRepository,Request $request)
    {
        $deliveryBills = $deliveryBillRepository->get($request,true);
        return view('admin.warehouse.deliverybills.index',compact('deliveryBills'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param DeliveryBillRepository $deliveryBillRepository
     * @return \Illuminate\Http\Response
     */
    public function create(DeliveryBillRepository $deliveryBillRepository)
    {
        $containers = $deliveryBillRepository->getContainers();
        return view('admin.warehouse.deliverybills.create',compact('containers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateDeliveryBillRequest $request
     * @param DeliveryBillRepository $deliveryBillRepository
     * @return \Illuminate\Http\Response
     */
    public function store(CreateDeliveryBillRequest $request, DeliveryBillRepository $deliveryBillRepository)
    {
        if ( $container = $deliveryBillRepository->store($request) ){
            session()->flash('alert-success', 'Delivery Bill Created Successfully');
            return redirect()->route('warehouse.delivery_bill.index');
        }
        session()->flash('alert-danger', $deliveryBillRepository->getError());
        return back()->withInput();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Warehouse\DeliveryBill  $deliveryBill
     * @return \Illuminate\Http\Response
     */
    public function show(DeliveryBill $deliveryBill)
    {
        $containers = $deliveryBill->containers()->paginate(50);
        return view('admin.warehouse.deliverybills.show',compact('containers'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Warehouse\DeliveryBill $deliveryBill
     * @param DeliveryBillRepository $deliveryBillRepository
     * @return \Illuminate\Http\Response
     */
    public function edit(DeliveryBill $deliveryBill, DeliveryBillRepository $deliveryBillRepository)
    {
        $containers = $deliveryBillRepository->getContainers();
        return view('admin.warehouse.deliverybills.edit',compact('containers','deliveryBill'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Warehouse\DeliveryBill $deliveryBill
     * @param DeliveryBillRepository $deliveryBillRepository
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DeliveryBill $deliveryBill, DeliveryBillRepository $deliveryBillRepository)
    {
        if ( $container = $deliveryBillRepository->update($request, $deliveryBill) ){
            session()->flash('alert-success', 'Delivery Bill Updated Successfully');
            return redirect()->route('warehouse.delivery_bill.index');
        }
        session()->flash('alert-danger', $deliveryBillRepository->getError());
        return back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Warehouse\DeliveryBill $deliveryBill
     * @param DeliveryBillRepository $deliveryBillRepository
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeliveryBill $deliveryBill, DeliveryBillRepository $deliveryBillRepository)
    {
        if ( $deliveryBill->isReady() ){
            abort(403,'Cannot Deleted This delivery Bill');
        }

        if ( $container = $deliveryBillRepository->delete($deliveryBill) ){
            session()->flash('alert-success', 'Delivery Bill Deleted Successfully');
            return redirect()->route('warehouse.delivery_bill.index');
        }

        session()->flash('alert-danger', $deliveryBillRepository->getError());
        return back()->withInput();
    }
}
