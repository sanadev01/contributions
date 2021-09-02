<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\State;
use App\Models\Recipient;
use App\Facades\USPSFacade;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use Illuminate\Support\Facades\Auth;
use App\Services\USPS\USPSShippingService;
use App\Services\Converters\UnitsConverter;
use App\Services\Calculators\WeightCalculator;

class USPSCalculatorController extends Controller
{
    public $error;
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
    public function store(Request $request)
    {
        $rules = [
            'origin_country' => 'required|numeric|exists:countries,id',
            'destination_country' => 'required|numeric|exists:countries,id',
            'destination_state' => 'required|exists:states,id',
            'destination_address' => 'required',
            'destination_city' => 'required',
            'destination_zipcode' => 'required',
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
            'destination_state' => 'Please Select Destination state',
            'destination_address' => 'Please Enter Destination address',
            'destination_city' => 'Please Enter Destination city',
            'destination_zipcode' => 'Please Enter Destination zipcode',
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
        $recipient->state_id = $request->destination_state;
        $recipient->address = $request->destination_address;
        $recipient->city = $request->destination_city;
        $recipient->zipcode = $request->destination_zipcode;

        $order = new Order();
        $order->id = 1;
        $order->user = Auth::user() ? Auth::user() :  User::where('role_id',1)->first();
        $order->sender_country_id = $request->origin_country;
        $order->width = $request->width;
        $order->height = $request->height;
        $order->length = $request->length;
        $order->weight = $request->weight;
        $order->measurement_unit = $request->unit;
        $order->recipient = $recipient;

        $shippingServices = collect() ;

        $usps_shippingService = new USPSShippingService($order);
        foreach (ShippingService::query()->active()->get() as $shippingService) {
            if ( $usps_shippingService->isAvailableFor($shippingService) ){
                    $shippingServices->push($shippingService);
            }
        }

        if($shippingServices->isEmpty()){
            $error = "Shipping Service not Available for the Country you have selected";
        }

        $shipping_rates = [];
        
        foreach ($shippingServices as $shippingService) {
            $response = USPSFacade::getPrice($order, $shippingService->service_sub_class);
            
            if($response->success == true)
            {
                array_push($shipping_rates , ['name'=> $shippingService->name , 'rate'=> $response->data['total_amount']]);
            }else {
                $this->error = $response->message;
            }
        }

        if($shipping_rates == null){
            session()->flash('alert-danger', $this->error);
        }

        if ($request->unit == 'kg/cm' ){
            $weightInOtherUnit = UnitsConverter::kgToPound($chargableWeight);
        }else{
            $weightInOtherUnit = UnitsConverter::poundToKg($chargableWeight);
        }
        
        return view('uspscalculator.show', compact('shipping_rates','order', 'weightInOtherUnit', 'chargableWeight'));
    }

    
}
