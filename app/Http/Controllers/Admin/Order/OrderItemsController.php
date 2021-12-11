<?php

namespace App\Http\Controllers\Admin\Order;

use App\Models\Order;
use App\Facades\UPSFacade;
use App\Facades\USPSFacade;
use App\Rules\NcmValidator;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use App\Http\Controllers\Controller;
use App\Repositories\OrderRepository;
use App\Services\UPS\UPSShippingService;
use App\Services\USPS\USPSShippingService;
use App\Http\Requests\Orders\OrderDetails\CreateRequest;

class OrderItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Order $order)
    {
        $this->authorize('editItems',$order);

        if ( !$order->recipient ){
            abort(404);
        }

        $shippingServices = collect() ;
        $error = null;

        if($order->recipient->country_id == Order::US)
        {
            $usps_shippingService = new USPSShippingService($order);

            foreach (ShippingService::query()->active()->get() as $shippingService) {
                if ( $usps_shippingService->isAvailableFor($shippingService) ){
                        $shippingServices->push($shippingService);
                }
            }

            $ups_shippingService = new UPSShippingService($order);
            foreach (ShippingService::query()->active()->get() as $shippingService) {
                if ( $ups_shippingService->isAvailableFor($shippingService) ){

                    $shippingServices->push($shippingService);
                }
            }

        } else {
            foreach (ShippingService::query()->has('rates')->active()->get() as $shippingService) {
                if ( $shippingService->isAvailableFor($order) ){
                    $shippingServices->push($shippingService);
                }elseif($shippingService->getCalculator($order)->getErrors() != null && $shippingServices->isEmpty()){
                    session()->flash('alert-danger',"Shipping Service not Available Error:{$shippingService->getCalculator($order)->getErrors()}");
                }
            }
        }
        if($shippingServices->isEmpty()){
            $error = "Shipping Service not Available for the Country you have selected";
        }

        if($shippingServices->contains('service_sub_class', ShippingService::USPS_PRIORITY) 
            || $shippingServices->contains('service_sub_class', ShippingService::USPS_FIRSTCLASS)
            || $shippingServices->contains('service_sub_class', ShippingService::UPS_GROUND))
        {
            if($order->user->usps != 1)
            {
                $error = "USPS is not enabled for this user";
                $shippingServices = collect() ;
            }
        }
        
        return view('admin.orders.order-details.index',compact('order','shippingServices', 'error'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRequest $request,Order $order, OrderRepository $orderRepository)
    {
        $this->authorize('editItems',$order);

        if ( !$order->recipient ){
            abort(404);
        }
        
        if ( $orderRepository->updateShippingAndItems($request,$order) ){
            session()->flash('alert-success','orders.Order Placed');
            if ($order->user->hasRole('wholesale') && $order->user->insurance == true) 
            {
                return redirect()->route('admin.orders.order-invoice.index',$order);# code...
            }
            return \redirect()->route('admin.orders.services.index',$order);
        }
        session()->flash('alert-danger','orders.Error While placing Order'." ".$orderRepository->getError());
        return \back()->withInput();
    }

    public function usps_rates(Request $request)
    {
        $order = Order::find($request->order_id);
        $response = USPSFacade::getPrice($order, $request->service);

        if($response->success == true)
        {
            return (Array)[
                'success' => true,
                'total_amount' => $response->data['total_amount'],
            ]; 
        }

        return (Array)[
            'success' => false,
            'message' => 'server error, could not get rates',
        ]; 

    }

    public function ups_rates(Request $request)
    {
        $order = Order::find($request->order_id);
        $response = UPSFacade::getRecipientRates($order, $request->service);
        
        if($response->success == false)
        {
            return (Array)[
                'success' => false,
                'error' => $response->error['response']['errors'][0]['message'],
            ];
        }

        return (Array)[
            'success' => true,
            'total_amount' => number_format($response->data['RateResponse']['RatedShipment']['TotalCharges']['MonetaryValue'], 2),
        ];
    }
}
