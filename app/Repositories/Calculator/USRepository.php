<?php

namespace App\Repositories\Calculator;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\State;
use App\Models\ShCode;
use App\Models\OrderItem;
use App\Models\Recipient;
use App\Facades\UPSFacade;
use App\Facades\USPSFacade;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UPSLabelRepository;
use App\Services\UPS\UPSShippingService;
use App\Repositories\USPSLabelRepository;
use App\Repositories\FedExLabelRepository;
use App\Services\USPS\USPSShippingService;
use App\Services\FedEx\FedExShippingService;
use App\Services\Calculators\WeightCalculator;

class USRepository
{
    public $upsShippingServices;
    public $uspsShippingServices;
    public $fedExShippingServices;

    public $shippingServices;
    
    protected $request;
    protected $recipient;
    public $order;
    public $uspsProfit;
    public $upsProfit;
    public $fedExProfit;
    public $shippingRates = [];
    public $shippingRatesWithProfit = [];
    public $uspsRatesWithProfit = [];
    public $upsShippingRates = [];
    public $upsRatesWithProfit = [];
    public $error;
    protected $chargableWeight;
    public $userLoggedIn = false;

    protected $tempOrder;
    protected $shippingService;
    protected $orderItems;


    public function __construct()
    {
        if (Auth::check()) 
        {
            $this->userLoggedIn = true;
        }

        $this->setUserProft();
        // $this->upsLabelRepository = new UPSLabelRepository();
        // $this->uspsLabelRepository = new USPSLabelRepository();
        // $this->fedExLabelRepository = new FedExLabelRepository();
    }
    
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
        $this->createTemporaryOrder();

        if ($request->has('items')) {
            $this->createOrderItems();
        }
        
        return $this->order;
    }

    public function getShippingServices()
    {
        $this->shippingServices = collect();

        $uspsShippingServices = $this->getUSPSShippingServices();

        $upsShippingServices = ($this->order->recipient->country_id == Order::US) ? $this->getUPSShippingServices() : null;

        $fedExShippingServices = ($this->order->recipient->country_id == Order::US) ? $this->getFedExShippingServices() : null;

        $this->shippingServices = $this->shippingServices->merge($uspsShippingServices)
                                            ->merge($upsShippingServices)
                                            ->merge($fedExShippingServices);

        return $this->shippingServices;
    }

    public function getRates()
    {
        if ($this->shippingServices->isEmpty()) {
            return $this->shippingRates;
        }

        if($this->shippingServices->contains('service_sub_class', ShippingService::USPS_PRIORITY) 
            || $this->shippingServices->contains('service_sub_class', ShippingService::USPS_FIRSTCLASS)
            || $this->shippingServices->contains('service_sub_class', ShippingService::USPS_PRIORITY_INTERNATIONAL)
            || $this->shippingServices->contains('service_sub_class', ShippingService::USPS_FIRSTCLASS_INTERNATIONAL))
        {
            $this->getUSPSRates();
        }

        if ($this->shippingServices->contains('service_sub_class', ShippingService::UPS_GROUND)) {
            $this->getUPSRates();
        }

        return $this->shippingRates;
    }

    public function getRatesWithProfit()
    {
        $this->shippingRatesWithProfit = [];

        if (!$this->shippingRates) {
            return $this->shippingRatesWithProfit;
        }

        foreach ($this->shippingRates as $serviceRate) {
            if ($serviceRate['service_sub_class'] == ShippingService::USPS_PRIORITY || 
                $serviceRate['service_sub_class'] == ShippingService::USPS_FIRSTCLASS ||
                $serviceRate['service_sub_class'] == ShippingService::USPS_PRIORITY_INTERNATIONAL ||
                $serviceRate['service_sub_class'] == ShippingService::USPS_FIRSTCLASS_INTERNATIONAL) 
            {
                $profit = $serviceRate['rate'] * ($this->uspsProfit / 100);

                $rate = $serviceRate['rate'] + $profit;

                array_push($this->shippingRatesWithProfit, ['name'=> $serviceRate['name'], 'service_sub_class' => $serviceRate['service_sub_class'], 'rate'=> number_format($rate, 2)]);
            }

            if($serviceRate['service_sub_class'] == ShippingService::UPS_GROUND){
                $profit = $serviceRate['rate'] * ($this->upsProfit / 100);

                $rate = $serviceRate['rate'] + $profit;

                array_push($this->shippingRatesWithProfit, ['name'=> $serviceRate['name'], 'service_sub_class' => $serviceRate['service_sub_class'], 'rate'=> number_format($rate, 2)]);
            }
        }

        return $this->shippingRatesWithProfit;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getUserLoggedInStatus()
    {
        return $this->userLoggedIn;
    }

    public function getchargableWeight()
    {
        return $this->chargableWeight;
    }

    private function setUserProft()
    {
        if ($this->userLoggedIn) {
            $this->uspsProfit = setting('usps_profit', null, auth()->user()->id);
            $this->upsProfit = setting('ups_profit', null, auth()->user()->id);
            $this->fedexProfit = setting('fedex_profit', null, auth()->user()->id);
        }

        if($this->uspsProfit == null || $this->uspsProfit == 0)
        {
            $this->uspsProfit = setting('usps_profit', null, User::ROLE_ADMIN);
        }

        if($this->upsProfit == null || $this->upsProfit == 0)
        {
            $this->upsProfit = setting('ups_profit', null, User::ROLE_ADMIN);
        }

        if($this->fedexProfit == null || $this->fedexProfit == 0)
        {
            $this->fedexProfit = setting('fedex_profit', null, User::ROLE_ADMIN);
        }
    }

    private function createRecipient()
    {
        $recipient = new Recipient();
        $recipient->first_name = 'Marcio';
        $recipient->last_name = 'Fertias';
        $recipient->phone = '+13058885191';
        $recipient->email = 'homedelivery@homedeliverybr.com';
        $recipient->country_id = (int)$this->request->destination_country;
        $recipient->state_id = State::where([['code', $this->request->recipient_state],['country_id', $this->request->destination_country]])->first()->id;
        $recipient->address = $this->request->recipient_address;
        $recipient->city = $this->request->recipient_city;
        $recipient->zipcode = $this->request->recipient_zipcode;
        $recipient->account_type = 'individual';

        $this->recipient = $recipient;
    }

    private function createTemporaryOrder()
    {
        $order = new Order();
        $order->id = 1;
        $order->warehouse_number = 'WHR-HD001';
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

    private function createOrderItems()
    {
        $this->orderItems = collect();

        foreach ($this->request->items as $key => $item) {
            $orderItem = new OrderItem();
            $orderItem->id = $key + 1;
            $orderItem->sh_code = ShCode::first()->code;
            $orderItem->description = $item['description'];
            $orderItem->quantity = $item['quantity'];
            $orderItem->value = $item['value'];
            $orderItem->contains_battery = false;
            $orderItem->contains_perfume = false;
            $orderItem->contains_flammable_liquid = false;
            $this->orderItems->push($orderItem);
        }

        $this->order->items = $this->orderItems;

        return true;
    }

    private function getUSPSShippingServices()
    {
        $shippingServices = collect() ;

        $uspsShippingService = new USPSShippingService($this->order);
        foreach (ShippingService::query()->active()->get() as $shippingService) {
            
            if(optional($this->order->recipient)->country_id == Order::US){
                
                if ( $uspsShippingService->isAvailableFor($shippingService) ){
                    $shippingServices->push($shippingService);
                }

            }else{

                if ($uspsShippingService->isAvailableForInternational($shippingService)) {
                    $shippingServices->push($shippingService);
                }
            }
        }

        return $shippingServices;
    }

    private function getUPSShippingServices()
    {
        $shippingServices = collect() ;

        $upsShippingService = new UPSShippingService($this->order);
        foreach (ShippingService::query()->active()->get() as $shippingService) {
            
            if ( $upsShippingService->isAvailableFor($shippingService) ){
                $shippingServices->push($shippingService);
            }
        }

        return $shippingServices;
    }

    private function getFedExShippingServices()
    {
        $shippingServices = collect() ;

        $fedExShippingService = new FedExShippingService($this->order);
        foreach (ShippingService::query()->active()->get() as $shippingService) {
            
            if ( $fedExShippingService->isAvailableFor($shippingService) ){
                $shippingServices->push($shippingService);
            }
        }

        return $shippingServices;
    }

    private function getUSPSRates()
    {
        $uspsServices = $this->shippingServices->filter(function ($shippingService) {

            return ($shippingService->service_sub_class == ShippingService::USPS_FIRSTCLASS || 
                    $shippingService->service_sub_class == ShippingService::USPS_PRIORITY || 
                    $shippingService->service_sub_class == ShippingService::USPS_PRIORITY_INTERNATIONAL ||
                    $shippingService->service_sub_class == ShippingService::USPS_FIRSTCLASS_INTERNATIONAL);
        });
        
        if ($uspsServices->isEmpty()) {
            return false;
        }

        $request = ($this->request->has('to_herco')) ? $this->createRequest() : null;

        foreach ($uspsServices as $service) {
            
            if ($this->request->has('to_herco')) {
                $request->merge(['service' => $service->service_sub_class]);
            }

            $uspsResponse = ($this->request->has('to_herco')) ? USPSFacade::getSenderPrice($this->order, $request) 
                                                                : USPSFacade::getRecipientRates($this->order, $service->service_sub_class);
            if ($uspsResponse->success == true) {
                array_push($this->shippingRates , ['name'=> $service->name, 'service_sub_class' => $service->service_sub_class, 'rate'=> number_format($uspsResponse->data['total_amount'], 2)]);
            }else
            {
                $this->error = $uspsResponse->message;
            }
        }

        return;
    }

    private function getUPSRates()
    {
        $upsServices = $this->shippingServices->filter(function ($shippingService) {
            return $shippingService->service_sub_class == ShippingService::UPS_GROUND;
        });

        if ($upsServices->isEmpty()) {
            return false;
        }

        $request = ($this->request->has('to_herco')) ? $this->createRequest() : null;

        foreach ($upsServices as $service) 
        {
            if ($this->request->has('to_herco')) {
                $request->merge(['service' => $service->service_sub_class]);
            }
            $upsResponse = ($this->request->has('to_herco')) ? UPSFacade::getSenderPrice($this->order, $request) 
                                                                : UPSFacade::getRecipientRates($this->order, $service->service_sub_class);
            if($upsResponse->success == true)
            {
                array_push($this->shippingRates , ['name'=> $service->name , 'service_sub_class' => $service->service_sub_class, 'rate'=> number_format($upsResponse->data['RateResponse']['RatedShipment']['TotalCharges']['MonetaryValue'], 2)]);
            }else
            {
                $this->error = $upsResponse->error['response']['errors'][0]['message'] ?? 'Unknown Error';
            }
        }

        return;
    }

    private function createRequest()
    {
        return new Request([
            'sender_country_id' => $this->order->sender_country_id,
            'first_name' => $this->order->sender_first_name,
            'last_name' => $this->order->sender_last_name,
            'pobox_number' => $this->order->pobox_number,
            'sender_state' => $this->order->sender_state,
            'sender_city' => $this->order->sender_city,
            'sender_address' => $this->order->sender_address,
            'sender_zipcode' => $this->order->sender_zipcode,
        ]);
    }
}
