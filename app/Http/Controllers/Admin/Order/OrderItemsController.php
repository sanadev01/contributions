<?php

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Orders\OrderDetails\CreateRequest;
use App\Models\Order;
use App\Models\ShippingService;
use App\Repositories\OrderRepository;
use App\Rules\NcmValidator;
use Illuminate\Http\Request;

class OrderItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Order $order)
    {
        if ( !$order->recipient ){
            abort(404);
        }

        $shippingServices = collect() ;
        foreach (ShippingService::query()->active()->get() as $shippingService) {
            if ( $shippingService->isAvailableFor($order) ){
                $shippingServices->push($shippingService);
            }else{
                session()->flash('alert-danger',"Shipping Service not Available Error:{$shippingService->getCalculator($order)->getErrors()}");
            }
        }


        return view('admin.orders.order-details.index',compact('order','shippingServices'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRequest $request,Order $order, OrderRepository $orderRepository)
    {
        if ( !$order->recipient ){
            abort(404);
        }
        
        if ( $orderRepository->updateShippingAndItems($request,$order) ){
            session()->flash('alert-success','orders.Order Placed');
            return \redirect()->route('admin.orders.order-invoice.index',$order);
        }
        session()->flash('alert-danger','orders.Error While placing Order'." ".$orderRepository->getError());
        return \back()->withInput();
    }
}
