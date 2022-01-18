<?php

namespace App\Repositories\Calculator;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Recipient;
use App\Facades\USPSFacade;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use Illuminate\Support\Facades\Auth;
use App\Services\USPS\USPSShippingService;
use App\Services\Calculators\WeightCalculator;

class USCalculatorRepository
{
    protected $request;
    protected $recipient;
    public $order;
    public $uspsProfit;
    public $uspsShippingRates = [];
    public $uspsRatesWithProfit = [];
    public $error;
    protected $chargableWeight;
    public $userLoggedIn = false;

    public function handle($request)
    {
        $this->request = $request;

        $originalWeight =  $this->request->weight;
        if ( $this->request->unit == 'kg/cm' ){
            $volumetricWeight = WeightCalculator::getVolumnWeight($this->request->length,$this->request->width,$this->request->height,'cm');
            $this->chargableWeight = round($volumetricWeight >  $originalWeight ? $volumetricWeight :  $originalWeight,2);
        }else{
            $volumetricWeight = WeightCalculator::getVolumnWeight($this->request->length,$this->request->width,$this->request->height,'in');
            $this->chargableWeight = round($volumetricWeight >  $originalWeight ? $volumetricWeight :  $originalWeight,2);
        }

        $this->createRecipient();

        $this->createOrder();

        return $this->order;
    }

    public function setUserUSPSProfit()
    {
        if (Auth::check()) 
        {
            $this->uspsProfit = setting('usps_profit', null, auth()->user()->id);
            $this->userLoggedIn = true;
        }

        if($this->uspsProfit == null || $this->uspsProfit == 0)
        {
            $this->uspsProfit = setting('usps_profit', null, User::ROLE_ADMIN);
        }

        return $this->uspsProfit;
    }

    public function getUSPSRates($uspsShippingServices, $order)
    {
        if ($uspsShippingServices->isEmpty()) {
            return $this->uspsShippingRates;
        }
        $request = $this->createRequest($order);

        foreach ($uspsShippingServices as $shippingService) 
        {
            $requestBody = $this->mergeShippingServiceIntoRequest($request, $shippingService->service_sub_class);
            $response = USPSFacade::getSenderPrice($order, $requestBody);

            if ($response->success == true) {
                array_push($this->uspsShippingRates , ['name'=> $shippingService->name , 'rate'=> number_format($response->data['total_amount'], 2)]);
            }else
            {
                $this->error = $response->message;
            }
        }

        return $this->uspsShippingRates;
    }

    public function getUSPSRatesWithProfit()
    {
        $this->uspsRatesWithProfit = [];

        if (!$this->uspsShippingRates) {
            return $this->uspsRatesWithProfit;
        }

        foreach ($this->uspsShippingRates as $uspsRate) {
            $profit = $uspsRate['rate'] * ($this->uspsProfit / 100);

            $rate = $uspsRate['rate'] + $profit;

            array_push($this->uspsRatesWithProfit, ['name'=> $uspsRate['name'] , 'rate'=> number_format($rate, 2)]);
        }

        return $this->uspsRatesWithProfit;
    }

    public function getUSPSShippingServices($order)
    {
        $shippingServices = collect() ;

        $uspsShippingService = new USPSShippingService($order);
        foreach (ShippingService::query()->active()->get() as $shippingService) {
            if ( $uspsShippingService->isAvailableFor($shippingService) ){
                $shippingServices->push($shippingService);
            }
        }

        return $shippingServices;
    }

    public function getUserLoggedInStatus()
    {
        return $this->userLoggedIn;
    }

    public function getchargableWeight()
    {
        return $this->chargableWeight;
    }

    private function createRecipient()
    {
        $recipient = new Recipient();
        $recipient->country_id = $this->request->destination_country;
        $recipient->state_id = 4622;
        $recipient->address = '2200 NW 129TH AVE';
        $recipient->city = 'Miami';
        $recipient->zipcode = '33182';

        $this->recipient = $recipient;
    }

    private function createOrder()
    {
        $order = new Order();
        $order->id = 1;
        $order->user = Auth::user() ? Auth::user() :  User::where('role_id',1)->first();
        $order->sender_country_id = $this->request->origin_country;
        $order->sender_first_name = $order->user->name;
        $order->sender_last_name = $order->user->last_name ?? '';
        $order->sender_email = $order->user->email;
        $order->sender_phone = $order->user->phone;
        $order->pobox_number = $order->user->pobox_number;
        $order->sender_city = $this->request->sender_city;
        $order->sender_state = $this->request->sender_state;
        $order->sender_address = $this->request->sender_address;
        $order->sender_zipcode = $this->request->sender_zipcode;
        $order->order_date = Carbon::now();
        $order->width = $this->request->width;
        $order->height = $this->request->height;
        $order->length = $this->request->length;
        $order->weight = $this->request->weight;
        $order->measurement_unit = $this->request->unit;
        $order->recipient = $this->recipient;

        $this->order = $order;
    }

    private function createRequest($order)
    {
        return new Request([
            'sender_country_id' => $order->sender_country_id,
            'first_name' => $order->sender_first_name,
            'last_name' => $order->sender_last_name,
            'pobox_number' => $order->pobox_number,
            'sender_state' => $order->sender_state,
            'sender_city' => $order->sender_city,
            'sender_address' => $order->sender_address,
            'sender_zipcode' => $order->sender_zipcode,
        ]);
    }

    private function mergeShippingServiceIntoRequest($request, $serviceSubClassCode)
    {
        return $request->merge([
            'service' => $serviceSubClassCode,
        ]);
    }
}
