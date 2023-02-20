<?php

namespace App\Http\Controllers\Admin\Order;

use App\Models\Order;
use App\Models\Country;
use App\Models\Deposit;
use App\Facades\UPSFacade;
use App\Facades\USPSFacade;
use App\Facades\FedExFacade;
use Illuminate\Http\Request;
use App\Models\OrderTracking;
use App\Models\PaymentInvoice;
use App\Models\ShippingService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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
            'FEDEX_GROUND' => ShippingService::USPS_GROUND,
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
        $shippingService = $order->shippingService;
        
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
        if($this->orderRepository->GePSService($request->shipping_service_id)){
            $value = 0;
            if (count($request->items) >= 1) {
                foreach ($request->items as $key => $item) {
                    $value += ($item['value'])*($item['quantity']);
                }
            }
            if($value > 400) {
                session()->flash('alert-danger', 'Total Parcel Value cannot be more than $400');
                return back()->withInput();
            }
        }
        if(in_array($shippingService->service_sub_class, [ShippingService::GePS, ShippingService::GePS_EFormat, ShippingService::Prime5])  ) {
            if(count($request->items) > 2) {
                session()->flash('alert-danger', 'More than 3 Items are Not Allowed with the Selected Service');
                return back()->withInput();
            }
        }
        if(in_array($shippingService->service_sub_class, [ShippingService::GePS, ShippingService::GePS_EFormat, ShippingService::Post_Plus_Registered])) {
            if($order->measurement_unit == "lbs/in" && $order->weight > 4.40 || $order->measurement_unit == "kg/cm" && $order->weight > 2) {
                session()->flash('alert-danger', 'Parcel Weight cannot be more than 4.40 LBS / 2 KG. Please Update Your Parcel');
                return back()->withInput();
            }
            if($order->length+$order->width+$order->height > $shippingService->max_sum_of_all_sides) {
                session()->flash('alert-danger', 'Maximun Pacakge Size: The sum of the length, width and height cannot not be greater than 90 cm (l + w + h <= 90). Please Update Your Parcel');
                return back()->withInput();
            }
        }
        if($shippingService->service_sub_class == ShippingService::Post_Plus_EMS) {
            if($order->length+$order->width+$order->height > $shippingService->max_sum_of_all_sides) {
                session()->flash('alert-danger', 'Maximun Pacakge Size: The sum of the length, width and height cannot not be greater than 300 cm (l + w + h <= 300). Please Update Your Parcel');
                return back()->withInput();
            }
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
        $items = collect();
        if (!is_null($request->descp) && !is_null($request->qty) && !is_null($request->value)) {
            foreach ($request->descp as $key => $descp) {
                $items = $items->push((object)[
                    'description' => $descp,
                    'quantity' => $request->qty[$key],
                    'value' => $request->value[$key]
                ]);
            }
            $order = Order::find($request->order_id);
            $order->items = $items;
        } else {
            $order = Order::find($request->order_id);
        }
        $response = USPSFacade::getRecipientRates($order, $request->service);

        if ($response->success == true) {
            return (array)[
                'success' => true,
                'total_amount' => $response->data['total_amount'],
            ];
        }

        return (array)[
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

        return (array)[
            'success' => true,
            'total_amount' => number_format($response->data['RateResponse']['RatedShipment']['TotalCharges']['MonetaryValue'], 2),
        ];
    }

    public function fedExRates(Request $request)
    {
        $order = Order::find($request->order_id);
        $response = FedExFacade::getRecipientRates($order, $request->service);

        if ($response->success == false) {
            return (array)[
                'success' => false,
                'error' => $response->error['response']['errors'][0]['message'] ?? $response->error['errors'][0]['code'] . '-' . 'server error, could not get rates',
            ];
        }

        return (array)[
            'success' => true,
            'total_amount' => number_format($response->data['output']['rateReplyDetails'][0]['ratedShipmentDetails'][0]['totalNetFedExCharge'], 2),
        ];
    }

}
