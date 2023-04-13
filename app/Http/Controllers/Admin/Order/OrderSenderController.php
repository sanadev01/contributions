<?php

namespace App\Http\Controllers\Admin\Order;

use App\Models\Order;
use App\Models\State;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\OrderRepository;
use App\Http\Requests\Orders\Sender\CreateRequest;
use App\Models\Country;
use Auth;
class OrderSenderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Order $order)
    {
        $this->authorize('editSender',$order);
        if(!Auth::user()->isActive()){
            return redirect()->route('admin.modals.user.suspended');
        }
        $states = State::query()->where("country_id", Country::US)->get(["name","code","id"]);
        $floridaStateId = State::query()->where([['country_id', Country::US],['code', 'FL']])->first()->id;
        $countryChile = Country::Chile;
        $countryUS = Country::US;

        return view('admin.orders.sender.index',compact('order', 'states', 'floridaStateId', 'countryChile', 'countryUS'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRequest $request,Order $order,OrderRepository $orderRepository)
    {
        $this->authorize('editSender',$order);
        
        if ( $orderRepository->updateSenderAddress($request,$order) ){
            session()->flash('alert-success','orders.Sender Updated');
            return redirect()->route('admin.orders.recipient.index',$order->encrypted_id);
        }

        session()->flash('alert-success','orders.Sender Update Error');
        return back();
    }
}
