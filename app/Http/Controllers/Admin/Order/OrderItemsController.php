<?php

namespace App\Http\Controllers\Admin\Order;

use App\Models\Order;
use App\Facades\UPSFacade;
use App\Facades\USPSFacade;
use App\Rules\NcmValidator;
use App\Facades\FedExFacade;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use App\Http\Controllers\Controller;
use App\Repositories\OrderRepository;
use App\Services\UPS\UPSShippingService;
use App\Services\USPS\USPSShippingService;
use App\Services\FedEx\FedExShippingService;
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
        $chiliId =  Order::CHILE;
        $shippingServices = collect() ;
        $error = null;

        if($order->recipient->country_id == Order::US)
        {
            $uspsShippingService = new USPSShippingService($order);

            foreach (ShippingService::query()->active()->get() as $shippingService) {
                if ( $uspsShippingService->isAvailableFor($shippingService) ){
                        $shippingServices->push($shippingService);
                }
            }

            $upsShippingService = new UPSShippingService($order);
            foreach (ShippingService::query()->active()->get() as $shippingService) {
                if ( $upsShippingService->isAvailableFor($shippingService) ){

                    $shippingServices->push($shippingService);
                }
            }

            $fedExShippingService = new FedExShippingService($order);
            foreach (ShippingService::query()->active()->get() as $shippingService) {
                if ( $fedExShippingService->isAvailableFor($shippingService) ){
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
            $error = ($order->recipient->commune_id != null) ? "Shipping Service not Available for the Region you have selected" : "Shipping Service not Available for the Country you have selected";
        }

        if($shippingServices->contains('service_sub_class', ShippingService::USPS_PRIORITY) 
            || $shippingServices->contains('service_sub_class', ShippingService::USPS_FIRSTCLASS)
            || $shippingServices->contains('service_sub_class', ShippingService::UPS_GROUND)
            || $shippingServices->contains('service_sub_class', ShippingService::FEDEX_GROUND))
        {
            if(!setting('usps', null, $order->user->id))
            {
                $error = "USPS is not enabled for this user";
                $shippingServices = $shippingServices->filter(function ($shippingService, $key) {
                    return $shippingService->service_sub_class != ShippingService::USPS_PRIORITY &&
                        $shippingService->service_sub_class != ShippingService::USPS_FIRSTCLASS;
                });
            }
            if(!setting('ups', null, $order->user->id))
            {
                $error = "UPS is not enabled for this user";
                $shippingServices = $shippingServices->filter(function ($shippingService, $key) {
                    return $shippingService->service_sub_class != ShippingService::UPS_GROUND;
                });
            }
            if(!setting('fedex', null, $order->user->id))
            {
                $error = "FedEx is not enabled for this user";
                $shippingServices = $shippingServices->filter(function ($shippingService, $key) {
                    return $shippingService->service_sub_class != ShippingService::FEDEX_GROUND;
                });
            }

            if($shippingServices->isNotEmpty()){
                $error = null;
            }
        }

        
        if($order->recipient->country_id == Order::BRAZIL)
        {
            // If sinerlog is enabled for the user, then remove the Correios services
            if(setting('sinerlog', null, $order->user->id))
            {
                $shippingServices = $shippingServices->filter(function ($item, $key)  {
                    return $item->service_sub_class != '33162' && $item->service_sub_class != '33170' && $item->service_sub_class != '33197';
                });
            }

            // If sinerlog is not enabled for the user then remove Sinerlog services from shipping service
            if(!setting('sinerlog', null, $order->user->id))
            {
                $shippingServices = $shippingServices->filter(function ($item, $key)  {
                    return $item->service_sub_class != '33163' && $item->service_sub_class != '33171' && $item->service_sub_class != '33198';
                });
            }
            
            if($shippingServices->isEmpty()){
                $error = "Please check your parcel dimensions";
            }
        }

        return view('admin.orders.order-details.index',compact('order','shippingServices', 'error','chiliId'));
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
                'error' => $response->error['response']['errors'][0]['message'] ?? 'server error, could not get rates',
            ];
        }

        return (Array)[
            'success' => true,
            'total_amount' => number_format($response->data['RateResponse']['RatedShipment']['TotalCharges']['MonetaryValue'], 2),
        ];
    }

    public function fedExRates(Request $request)
    {
        $order = Order::find($request->order_id);
        $response = FedExFacade::getRecipientRates($order, $request->service);

        if ($response->success == false) {
            return (Array)[
                'success' => false,
                'error' => $response->error['response']['errors'][0]['message'] ?? 'server error, could not get rates',
            ];
        }

        return (Array)[
            'success' => true,
            'total_amount' => number_format($response->data['output']['rateReplyDetails'][0]['ratedShipmentDetails'][0]['totalNetFedExCharge'], 2),
        ];
    }
}
