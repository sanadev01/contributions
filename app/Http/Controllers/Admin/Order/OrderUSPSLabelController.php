<?php

namespace App\Http\Controllers\Admin\Order;

use App\Models\Order;
use App\Models\State;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use App\Http\Controllers\Controller;
use App\Repositories\USPSLabelRepository;
use App\Services\USPS\USPSShippingService;

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

        $shippingServices = collect() ;
        $error = null;

        $states = State::query()->where("country_id", 250)->get(["name","code","id"]);
        $usps_shippingService = new USPSShippingService($order);
        
        foreach (ShippingService::query()->active()->get() as $shippingService) {
            if ( $usps_shippingService->isAvailableFor($shippingService) ){
                    $shippingServices->push($shippingService);
            }
        }

        if($shippingServices->isEmpty()){
            $error = "Shipping Service not Available for the Country you have selected";
        }

        if($shippingServices->contains('service_sub_class', '3440') || $shippingServices->contains('service_sub_class', '3441'))
        {
            if($order->user->usps != 1)
            {
                $error = "USPS is not enabled for this user";
                $shippingServices = collect() ;
            }
        }

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

        return redirect()->back();
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
}
