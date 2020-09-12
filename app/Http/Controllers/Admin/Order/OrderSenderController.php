<?php

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Orders\Sender\CreateRequest;
use App\Models\Order;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;

class OrderSenderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Order $order)
    {
        return view('admin.orders.sender.index',compact('order'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRequest $request,Order $order,OrderRepository $orderRepository)
    {
        if ( $orderRepository->updateSenderAddress($request,$order) ){
            session()->flash('alert-success','Sender Updated');
            return redirect()->route('admin.orders.recipient.index',$order);
        }

        session()->flash('alert-success','Sender Update Error');
        return back();
    }
}
