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
        $this->authorize('editServices',$order);
        
        $order->load('services');
        $services = HandlingService::query()->active()->get();
        $services = $this->checkInsurance($services,$order);

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
        $this->authorize('editServices',$order);
        
        if ( $orderRepository->updateHandelingServices($request,$order) ){
            session()->flash('alert-success','orders.Services Updated');
            return redirect()->route('admin.orders.order-invoice.index',$order);
        }

        session()->flash('alert-success','orders.Error Updateding Services');
        return \back();
    }
    
    private function checkInsurance($services, $order)
    {
        if($order->user->insurance == true)
        {
            $services = $services->filter(function ($service) {
                return $service->name != 'Insurance';
            });
        }

        if ($order->user->hasRole('wholesale'))
        {
            $services = $services->filter(function ($service) {
                return $service->name == 'Insurance';
            });
        }

        return $services;
    }
}
