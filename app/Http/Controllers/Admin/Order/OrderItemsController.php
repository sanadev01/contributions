<?php

namespace App\Http\Controllers\Admin\Order;

use App\Models\Order;
use App\Models\Country;
use App\Facades\UPSFacade;
use App\Facades\USPSFacade;
use App\Facades\FedExFacade;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use App\Http\Controllers\Controller;
use App\Repositories\OrderRepository;
use App\Http\Requests\Orders\OrderDetails\CreateRequest;

class OrderItemsController extends Controller
{
    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }
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

        $shippingServices = $this->orderRepository->getShippingServices($order);
        $error = $this->orderRepository->getShippingServicesError();

        if ($error) {
            session()->flash($error);
        }

        $countryConstants = [
            'Brazil' => Country::Brazil,
            'Chile' => Country::Chile,
            'Colombia' => Country::COLOMBIA,
            'US' => Country::US,
        ];

        $shippingServiceCodes = [
            'USPS_PRIORITY' => ShippingService::USPS_PRIORITY,
            'USPS_FIRSTCLASS' => ShippingService::USPS_FIRSTCLASS,
            'USPS_PRIORITY_INTERNATIONAL' => ShippingService::USPS_PRIORITY_INTERNATIONAL,
            'USPS_FIRSTCLASS_INTERNATIONAL' => ShippingService::USPS_FIRSTCLASS_INTERNATIONAL,
            'UPS_GROUND' => ShippingService::UPS_GROUND,
            'FEDEX_GROUND' => ShippingService::FEDEX_GROUND,
            'COLOMBIA_Standard' => ShippingService::COLOMBIA_Standard,
        ];
        
        return view('admin.orders.order-details.index',compact('order','shippingServices', 'error', 'countryConstants', 'shippingServiceCodes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRequest $request,Order $order)
    {
        $this->authorize('editItems',$order);

        if ( !$order->recipient ){
            abort(404);
        }

        if($this->orderRepository->serviceRequireFreight($request->shipping_service_id)){
            $request->validate([
                'user_declared_freight' => 'bail|required|gt:0',
            ], [
                'user_declared_freight.required' => __('validation.required', ['attribute' => 'shipping service rate not availaible for this service']),
                'user_declared_freight.gt' => __('validation.gt', ['attribute' => 'shipping service rate not availaible for this service']),
            ]);
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
        
        if ( $this->orderRepository->updateShippingAndItems($request,$order) ){
            session()->flash('alert-success','orders.Order Placed');
            if ($order->user->hasRole('wholesale') && $order->user->insurance == true) 
            {
                return redirect()->route('admin.orders.order-invoice.index',$order);# code...
            }
            return \redirect()->route('admin.orders.services.index',$order);
        }
        return \back()->withInput();
    }

    public function uspsRates(Request $request)
    {
        $order = Order::find($request->order_id);
        $response = USPSFacade::getRecipientRates($order, $request->service);

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
