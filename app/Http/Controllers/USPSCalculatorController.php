<?php

namespace App\Http\Controllers;

use App\Models\State;
use App\Facades\USPSFacade;
use Illuminate\Http\Request;
use App\Services\Converters\UnitsConverter;
use App\Repositories\USPSCalculatorRepository;
use App\Http\Requests\Calculator\USPSCalculatorRequest;
use App\Repositories\Calculator\USCalculatorRepository;

class USPSCalculatorController extends Controller
{
    public $error;
    public $shipping_rates = [];
    public $user_api_profit;
    public $userLoggedIn = false;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $states = State::query()->where("country_id", 250)->get(["name","code","id"]);
        return view('uspscalculator.index', compact('states'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(USPSCalculatorRequest $request, USCalculatorRepository $usCalculatorRepository)
    {
        $order = $usCalculatorRepository->handle($request);
        $uspsShippingServices = $usCalculatorRepository->getUSPSShippingServices($order);

        $usCalculatorRepository->setUserUSPSProfit();

        if($uspsShippingServices->isEmpty()){
            $error = 'Shipping Service not Available for the Country you have selected';
        }

        $uspsRatesWithoutProfit = $usCalculatorRepository->getUSPSRates($uspsShippingServices, $order);

        
        foreach ($uspsShippingServices as $shippingService) {

            $request_data = $this->create_request($order, $shippingService->service_sub_class);
            $response = USPSFacade::getSenderPrice($order, $request_data);
           
            if($response->success == true)
            {
                array_push($this->shipping_rates , ['name'=> $shippingService->name , 'rate'=> number_format($response->data['total_amount'], 2)]);

            }else {
                $this->error = $response->message;
            }
        }

        if($this->shipping_rates == null){

            session()->flash('alert-danger', $this->error);

        }else {
            // rates without profit
            $usps_rates = $this->shipping_rates;
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
        return view('uspscalculator.show', compact('usps_rates','shipping_rates','order', 'weightInOtherUnit', 'chargableWeight', 'userLoggedIn'));
    }

    public function addProfit($shipping_rates)
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

    public function buy_usps_label(Request $request)
    {
        $usps_calculatorRepository = new USPSCalculatorRepository();
        $order = $usps_calculatorRepository->handle($request);

        $error = $usps_calculatorRepository->getUSPSErrors();

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
