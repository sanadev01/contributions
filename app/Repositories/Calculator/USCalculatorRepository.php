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
    public $shippingRates = [];
    public $error;

    public function handle($request)
    {
        $this->request = $request;

        $originalWeight =  $this->request->weight;
        if ( $this->request->unit == 'kg/cm' ){
            $volumetricWeight = WeightCalculator::getVolumnWeight($this->request->length,$this->request->width,$this->request->height,'cm');
            $chargableWeight = round($volumetricWeight >  $originalWeight ? $volumetricWeight :  $originalWeight,2);
        }else{
            $volumetricWeight = WeightCalculator::getVolumnWeight($this->request->length,$this->request->width,$this->request->height,'in');
            $chargableWeight = round($volumetricWeight >  $originalWeight ? $volumetricWeight :  $originalWeight,2);
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
            $this->uspsProfit = setting('usps_profit', null, 1);
        }

        return $this->uspsProfit;
    }

    public function getUSPSRates($uspsShippingServices, $order)
    {
        foreach ($uspsShippingServices as $shippingService) 
        {
            $requestBody = $this->createRequest($order, $shippingService->service_sub_class);
            $response = USPSFacade::getSenderPrice($order, $requestBody);

            if ($response->success == true) {
                array_push($this->shippingRates , ['name'=> $shippingService->name , 'rate'=> number_format($response->data['total_amount'], 2)]);
            }else
            {
                $this->error = $response->message;
            }
        }

        if ($this->shippingRates) {
            $this->addProfit();
        }

        return $this->shippingRates;
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

    private function createRequest($order, $serviceSubClassCode)
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
            'service' => $serviceSubClassCode,
        ]);
    }

    private function addProfit()
    {
        # code...
    }
}
