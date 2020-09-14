<?php

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use App\Models\HandlingService;
use App\Models\Order;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;

class OrderServicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Order $order)
    {
        $order->load('services');
        $services = HandlingService::query()->active()->get();
        return view('admin.orders.services.index',compact('order','services'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,Order $order, OrderRepository $orderRepository)
    {
        if ( $orderRepository->updateHandelingServices($request,$order) ){
            session()->flash('alert-success','Services Updated');
            return redirect()->route('admin.orders.order-details.index',$order);
        }

        session()->flash('alert-success','error Updateding Services');
        return \back();
    }
}
