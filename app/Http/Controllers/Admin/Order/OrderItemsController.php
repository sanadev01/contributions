<?php

namespace App\Http\Controllers\Admin\Order;

use App\Models\Order;
use App\Facades\UPSFacade;
use App\Facades\USPSFacade;
use App\Facades\FedExFacade;
use Illuminate\Http\Request;
use App\Services\GSS\Client;
use App\Models\ShippingService;
use App\Http\Controllers\Controller;
use App\Repositories\OrderRepository;
use App\Http\Requests\Orders\OrderDetails\CreateRequest;
use App\Models\ShCode;
use App\Models\User;

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
        $chileCountryId =  Order::CHILE;
        $usCountryId =  Order::US;
        $shippingServices = $this->orderRepository->getShippingServices($order);
        $error = $this->orderRepository->getShippingServicesError();

        if ($error) {
            session()->flash($error);
        }
        
        return view('admin.orders.order-details.index',compact('order','shippingServices', 'error','chileCountryId', 'usCountryId'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRequest $request,Order $order)
    {
        if($order->items->isEmpty()){
            session()->flash('alert-danger', 'Please add atleast one item!');
            return redirect()->route('admin.orders.order-details.index',[$order->id]);
        }
        $shippingService = ShippingService::find($request->shipping_service_id);

        $this->authorize('editItems',$order);

        if ( !$order->recipient ){
            abort(404);
        }
        if ($this->orderRepository->domesticService($request->shipping_service_id) || $order->shippingService->is_total_express) {
            $request->validate([
                'user_declared_freight' => 'bail|required|gt:0',
            ], [
                'user_declared_freight.required' => __('validation.required', ['attribute' => 'shipping service rate not availaible for this service']),
                'user_declared_freight.gt' => __('validation.gt', ['attribute' => 'shipping service rate not availaible for this service']),
            ]);
        }
        if($this->orderRepository->GePSService($request->shipping_service_id)){
            $value = 0;
            if (count($order->items) >= 1) {
                foreach ($order->items as $key => $item) {
                    $value += ($item['value'])*($item['quantity']);
                }
            }
            if($value > 400) {
                session()->flash('alert-danger', 'Total Parcel Value cannot be more than $400');
                return back()->withInput();
            }
        }
        if(in_array($shippingService->service_sub_class, [ShippingService::GePS, ShippingService::GePS_EFormat, ShippingService::Parcel_Post, ShippingService::Prime5RIO])  ) {
            if(count($order->items) > 5) {
                 
                session()->flash('alert-danger', 'More than 5 Items are Not Allowed with the Selected Service');
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
            ->find($request->shipping_service_id);
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
            if ($this->deleteInvalidShCode($order, $shippingService)){
                session()->flash('alert-danger','Please remove invalid sh code and continue!');
                return redirect()->route('admin.orders.order-details.index',[$order->encrypted_id]);
            }
            session()->flash('alert-success','orders.Order Placed');
            if ($order->user->hasRole('wholesale') && $order->user->insurance == true) 
            {
                return redirect()->route('admin.orders.order-invoice.index',$order->encrypted_id);# code...
            }
            return \redirect()->route('admin.orders.services.index',$order->encrypted_id);
        }
        return \back()->withInput();
    }
    public function deleteInvalidShCode($order, $shippingService)
    {
        $itemType = optional($shippingService)->is_total_express ? 'Courier' : 'Postal (Correios)';
        $itemsToDelete = $order->items->filter(function ($item) use($itemType){
            return ShCode::where('code',$item->sh_code)->where('type',$itemType)->first()==null;  
        });
        return $itemsToDelete->count()>0;
    }

    public function uspsRates(Request $request)
    {
        $items = collect();
        if(!is_null($request->descp) && !is_null($request->qty) && !is_null($request->value)){
            foreach ($request->descp as $key => $descp) {
                $items = $items->push((object)[
                    'description' => $descp, 
                    'quantity' => $request->qty[$key], 
                    'value' => $request->value[$key]
                ]);
            }
            $order = Order::find($request->order_id);
            $order->items = $items;
        }else{
            $order = Order::find($request->order_id);
        }
        $response = USPSFacade::getRecipientRates($order, $request->service);

        if($response->success == true)
        {
            $rate =  $response->data['total_amount'];
            \Log::info('with out profit');
            \Log::info($rate);
            $profit = setting('usps_profit', null, $order->user_id)??(int) setting('usps_profit', null, User::ROLE_ADMIN);
            $rate = $rate + ($rate/100) * $profit;
            \Log::info('with profit');
            \Log::info($rate);             
            return (Array)[
                'success' => true,
                'total_amount' => $rate,
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
                'error' => $response->error['response']['errors'][0]['message'] ?? $response->error['errors'][0]['code'].'-'.'server error, could not get rates',
            ];
        }

        return (Array)[
            'success' => true,
            'total_amount' => number_format($response->data['output']['rateReplyDetails'][0]['ratedShipmentDetails'][0]['totalNetFedExCharge'], 2),
        ];
    }

    public function GSSRates(Request $request)
    {
        $client = new Client();
        $response =  $client->getServiceRates($request);
        $data = $response->getData();
        if ($data->isSuccess && $data->output > 0){
            return (array)[
                'success' => true,
                'total_amount' => number_format($data->output, 2),
            ];
        } else {
            return (array)[
                'success' => false,
                'error' => 'server error occured',
            ];
        }

        
    }
}
