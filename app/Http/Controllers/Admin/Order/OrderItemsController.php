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

                    $response = UPSFacade::getRecipientRates($order, $shippingService->service_sub_class);
                    if($response->success == true)
                    {
                        $shippingServices->push($shippingService);
                    }
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

        if($shippingServices->contains('service_sub_class', '3440') || $shippingServices->contains('service_sub_class', '3441'))
        {
            if($order->user->usps != 1)
            {
                $error = "USPS is not enabled for this user";
                $shippingServices = collect() ;
            }
        }

        
        if($order->recipient->country_id == Order::BRAZIL)
        {
            // If sinerlog is enabled for the user, then remove the Correios services
            if($order->user->sinerlog == true)
            {
                $shippingServices = $shippingServices->filter(function ($item, $key)  {
                    return $item->service_sub_class != '33162' && $item->service_sub_class != '33170' && $item->service_sub_class != '33197';
                });
            }

            // If sinerlog is not enabled for the user then remove Sinerlog services from shipping service
            if($order->user->sinerlog != true)
            {
                $shippingServices = $shippingServices->filter(function ($item, $key)  {
                    return $item->service_sub_class != '33163' && $item->service_sub_class != '33171' && $item->service_sub_class != '33198';
                });
            }
            
            if($shippingServices->isEmpty()){
                $error = "Please check your parcel dimensions";
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

        /**
         * Sinerlog modification
         * Get total of items declared to check if them more than US$ 50 when Sinerlog Small Parcels was selected
         */
        $shipping_service_data = \DB::table('shipping_services')
            ->select('max_sum_of_all_products','api','service_api_alias')
            ->find($request->shipping_service_id)
        ;
        if ($shipping_service_data->api == 'sinerlog' && $shipping_service_data->service_api_alias == 'XP') {
            
            $sum_of_all_products = 0;
            foreach ($request->get('items',[]) as $item) {
                $sum_of_all_products = $sum_of_all_products + (optional($item)['value'] * optional($item)['quantity']);
            }

            if ($sum_of_all_products > $shipping_service_data->max_sum_of_all_products) {
                session()->flash('alert-danger','The total amount of items declared must be lower or equal US$ 50.00 for selected shipping serivce.');
                return \back()->withInput();
            }

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
            'total_amount' => number_format($response->data['FreightRateResponse']['TotalShipmentCharge']['MonetaryValue'], 2),
        ];
    }
}
