<?php

namespace App\Http\Controllers\Admin\Order;

use App\Models\Order;
use App\Models\State;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\USPSLabelRepository;

class OrderUSPSLabelController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Order $order)
    {
        $this->authorize('canPrintLable',$order);
        
        $usps_labelRepository = new USPSLabelRepository();
        $shippingServices = $usps_labelRepository->getShippingServices($order);
        $error = $usps_labelRepository->getUSPSErrors();
        if($error != null)
        {
            session()->flash('alert-danger', $error);
        }
        
        $states = State::query()->where("country_id", 250)->get(["name","code","id"]);
        
        return view('admin.orders.label.usps',compact('order', 'states', 'shippingServices', 'error'));
    }

    public function usps_sender_rates(Request $request)
    {
        $usps_labelRepository = new USPSLabelRepository();
        return $usps_labelRepository->getRates($request);
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
    public function store(Request $request, Order $order)
    {
        $usps_labelRepository = new USPSLabelRepository();
        $usps_labelRepository->buyLabel($request, $order);

        $error = $usps_labelRepository->getUSPSErrors();
        if($error != null)
        {
            session()->flash('alert-danger', $error);
            return \back()->withInput();
        }

        $order->refresh();

        return redirect()->route('admin.orders.usps-label.index', $order->id);
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
    public function destroy($id)
    {
        //
    }

    public function uspsBulkView()
    {
        return view('admin.orders.label.usps-bulk-label');
    }
}
