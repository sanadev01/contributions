<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\State;
use App\Models\Recipient;
use App\Facades\UPSFacade;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use Illuminate\Support\Facades\Auth;
use App\Services\UPS\UPSShippingService;
use App\Services\Converters\UnitsConverter;
use App\Repositories\UPSCalculatorRepository;
use App\Services\Calculators\WeightCalculator;

class UPSCalculatorController extends Controller
{
    public $error;
    public $shipping_rates = [];
    public $user_api_profit;
    public $userLoggedIn = false;

    public function index()
    {
        $states = State::query()->where("country_id", 250)->get(["name","code","id"]);
        return view('upscalculator.index', compact('states'));
    }

    public function store(Request $request)
    {   
        $rules = [
            'origin_country' => 'required|numeric|exists:countries,id',
            'destination_country' => 'required|numeric|exists:countries,id',
            'sender_state' => 'required|exists:states,code',
            'sender_address' => 'required',
            'sender_city' => 'required',
            'sender_zipcode' => 'required',
            'height' => 'sometimes|numeric',
            'width' => 'sometimes|numeric',
            'length' => 'sometimes|numeric',
            'unit' => 'required|in:lbs/in,kg/cm',
        ];
        
        if($request->unit == 'kg/cm'){
            $rules['weight'] = 'sometimes|numeric|max:30';
        }else{
            $rules['weight'] = 'sometimes|numeric|max:66.15';
        }

        $message = [
            'origin_country' => 'Please Select Origin country',
            'destination_country' => 'Please Select Destination country',
            'sender_state' => 'Please Select Origin state',
            'sender_address' => 'Please Enter Destination address',
            'sender_city' => 'Please Enter Destination city',
            'sender_zipcode' => 'Please Enter Destination zipcode',
            'weight' => 'Please Enter weight',
            'weight.max' => 'weight exceed the delivery of USPS',
            'height' => 'Please Enter height',
            'width' => 'Please Enter width',
            'length' => 'Please Enter length',
            'unit' => 'Please Select Measurement Unit ',
        ];
        
        $this->validate($request, $rules, $message);
        
        $originalWeight =  $request->weight;
        if ( $request->unit == 'kg/cm' ){
            $volumetricWeight = WeightCalculator::getVolumnWeight($request->length,$request->width,$request->height,'cm');
            $chargableWeight = round($volumetricWeight >  $originalWeight ? $volumetricWeight :  $originalWeight,2);
        }else{
            $volumetricWeight = WeightCalculator::getVolumnWeight($request->length,$request->width,$request->height,'in');
            $chargableWeight = round($volumetricWeight >  $originalWeight ? $volumetricWeight :  $originalWeight,2);
        }

        $recipient = new Recipient();
        $recipient->country_id = $request->destination_country;
        $recipient->state_id = 4622;
        $recipient->address = '2200 NW 129TH AVE';
        $recipient->city = 'Miami';
        $recipient->zipcode = '33182';

        $order = new Order();
        $order->id = 1;
        $order->user = Auth::user() ? Auth::user() :  User::where('role_id',1)->first();
        $order->sender_country_id = $request->origin_country;
        $order->sender_first_name = $order->user->name;
        $order->sender_last_name = $order->user->last_name ?? '';
        $order->sender_email = $order->user->email;
        $order->sender_phone = $order->user->phone;
        $order->pobox_number = $order->user->pobox_number;
        $order->sender_city = $request->sender_city;
        $order->sender_state = $request->sender_state;
        $order->sender_address = $request->sender_address;
        $order->sender_zipcode = $request->sender_zipcode;
        $order->order_date = Carbon::now();
        $order->width = $request->width;
        $order->height = $request->height;
        $order->length = $request->length;
        $order->weight = $request->weight;
        $order->measurement_unit = $request->unit;
        $order->recipient = $recipient;

        $shippingServices = collect();
        $this->checkUser();

        $ups_shippingService = new UPSShippingService($order);
        foreach (ShippingService::query()->active()->get() as $shippingService) {
            if ( $ups_shippingService->isAvailableFor($shippingService) ){
                    $shippingServices->push($shippingService);
            }
        }
        
        if($shippingServices->isEmpty()){
            $error = "Shipping Service not Available for the Country you have selected";
        }

        foreach ($shippingServices as $shippingService) {

            $request_data = $this->create_request($order, $shippingService->service_sub_class);
            $response = UPSFacade::getSenderPrice($order, $request_data);
           
            if($response->success == true)
            {
                array_push($this->shipping_rates , ['name'=> $shippingService->name , 'rate'=> number_format($response->data['FreightRateResponse']['TotalShipmentCharge']['MonetaryValue'], 2)]);

            }else {
                $this->error = $response->error['response']['errors'][0]['message'];
            }
        }

        if($this->shipping_rates == null){
            $ups_rates = $this->shipping_rates;
            $shipping_rates = $this->shipping_rates;
            session()->flash('alert-danger', $this->error);
        } else {
            // rates without profit
            $ups_rates = $this->shipping_rates;
            // rates with profit
            $this->addProfit($this->shipping_rates);
            $shipping_rates = $this->shipping_rates;
        }

        if ($request->unit == 'kg/cm' ){
            $weightInOtherUnit = UnitsConverter::kgToPound($chargableWeight);
        }else{
            $weightInOtherUnit = UnitsConverter::poundToKg($chargableWeight);
        }

        $userLoggedIn = $this->userLoggedIn;
        
        // $shipping_rates = $this->shipping_rates;
        return view('upscalculator.show', compact('ups_rates','shipping_rates','order', 'weightInOtherUnit', 'chargableWeight', 'userLoggedIn'));

    }

    private function checkUser()
    {
        if (Auth::check()) 
        {
            $this->user_api_profit = Auth::user()->api_profit;
            $this->userLoggedIn = true;

        }

        if($this->user_api_profit == 0)
        {
            $admin = User::where('role_id',1)->first();

            $this->user_api_profit = $admin->api_profit;
        }

        return;
    }

    private function create_request($order, $service)
    {
        $request = (Object)[
            'sender_country_id' => $order->sender_country_id,
            'first_name' => $order->sender_first_name,
            'last_name' => $order->sender_last_name,
            'pobox_number' => $order->pobox_number,
            'sender_state' => $order->sender_state,
            'sender_city' => $order->sender_city,
            'sender_address' => $order->sender_address,
            'sender_zipcode' => $order->sender_zipcode,
            'service' => $service,
        ];
        
        
        return $request;
    }

    private function addProfit($shipping_rates)
    {
        $this->shipping_rates = [];
        foreach ($shipping_rates as  $shipping_rate) 
        {
            $profit = $shipping_rate['rate'] * ($this->user_api_profit / 100);

            $rate = $shipping_rate['rate'] + $profit;

            array_push($this->shipping_rates , ['name'=> $shipping_rate['name'] , 'rate'=> number_format($rate, 2)]);
        }

        return true;
    }

    public function buy_ups_label(Request $request)
    {
        $ups_calculatorRepository = new UPSCalculatorRepository();
        $order = $ups_calculatorRepository->handle($request);

        $error = $ups_calculatorRepository->getUPSErrors();

        if($error != null)
        {
            return (Array)[
                'success' => false,
                'message' => $error,
            ]; 
        }

        return (Array)[
            'success' => true,
            'message' => 'USPS label has been generated successfully',
            'path' => route('admin.orders.label.index', $order->id)
        ]; 
    }
}
