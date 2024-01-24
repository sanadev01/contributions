<?php

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Orders\Recipient\CreateRequest;
use App\Models\Order;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;

class OrderRecipientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Order $order)
    {
        $this->authorize('editReceipient',$order);
        return view('admin.orders.recipient.index',compact('order'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRequest $request, Order $order, OrderRepository $orderRepository)
    {
        $this->authorize('editReceipient',$order);
        if ( $orderRepository->updateRecipientAddress($request,$order) ){
            session()->flash('alert-success',"orders.Recipient Updated");
            return redirect()->route('admin.orders.order-details.index',$order->encrypted_id);
        }

        session()->flash('alert-danger','orders.Recipient Update Error');
        return \back();
    }
}
