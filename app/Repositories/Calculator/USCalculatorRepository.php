<?php

namespace App\Repositories\Calculator;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\State;
use App\Models\ShCode;
use App\Models\Country;
use App\Models\OrderItem;
use App\Models\Recipient;
use App\Facades\UPSFacade;
use App\Facades\USPSFacade;
use App\Facades\FedExFacade;
use Illuminate\Http\Request;
use App\Models\PaymentInvoice;
use App\Models\ShippingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UPSLabelRepository;
use App\Services\UPS\UPSShippingService;
use App\Repositories\USPSLabelRepository;
use App\Repositories\FedExLabelRepository;
use App\Services\USPS\USPSShippingService;
use App\Services\Excel\Export\ExportUSRates;
use App\Services\FedEx\FedExShippingService;
use App\Services\Calculators\WeightCalculator;

class USCalculatorRepository
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

    protected $upsLabelRepository;
    protected $uspsLabelRepository;
    protected $fedExLabelRepository;


    public function __construct()
    {
        if (Auth::check()) 
        {
            $this->userLoggedIn = true;
        }

        $this->setUserProft();
        $this->upsLabelRepository = new UPSLabelRepository();
        $this->uspsLabelRepository = new USPSLabelRepository();
        $this->fedExLabelRepository = new FedExLabelRepository();
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
            || $this->shippingServices->contains('service_sub_class', ShippingService::USPS_FIRSTCLASS_INTERNATIONAL)
            || $this->shippingServices->contains('service_sub_class', ShippingService::USPS_GROUND))
        {
            $this->getUSPSRates();
        }

        if ($this->shippingServices->contains('service_sub_class', ShippingService::UPS_GROUND)) {
            $this->getUPSRates();
        }

        if ($this->shippingServices->contains('service_sub_class', ShippingService::FEDEX_GROUND)) {
            $this->getFedExRates();
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
                $serviceRate['service_sub_class'] == ShippingService::USPS_FIRSTCLASS_INTERNATIONAL ||
                $serviceRate['service_sub_class'] == ShippingService::USPS_GROUND) 
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

            if ($serviceRate['service_sub_class'] == ShippingService::FEDEX_GROUND) {
                $profit = $serviceRate['rate'] * ($this->fedExProfit / 100);

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

    public function download($rates, $order, $chargableWeight, $weightInOtherUnit)
    {
        $exportService = new ExportUSRates($rates, $order, $chargableWeight, $weightInOtherUnit);

        return $exportService->handle();
    }

    private function setUserProft()
    {
        if ($this->userLoggedIn) {
            $this->uspsProfit = setting('usps_profit', null, auth()->user()->id);
            $this->upsProfit = setting('ups_profit', null, auth()->user()->id);
            $this->fedExProfit = setting('fedex_profit', null, auth()->user()->id);
        }

        if($this->uspsProfit == null || $this->uspsProfit == 0)
        {
            $this->uspsProfit = setting('usps_profit', null, User::ROLE_ADMIN);
        }

        if($this->upsProfit == null || $this->upsProfit == 0)
        {
            $this->upsProfit = setting('ups_profit', null, User::ROLE_ADMIN);
        }

        if($this->fedExProfit == null || $this->fedExProfit == 0)
        {
            $this->fedExProfit = setting('fedex_profit', null, User::ROLE_ADMIN);
        }
    }

    public function executeForLabel($request)
    {
        $this->request = $request;
        $this->tempOrder = $request->temp_order;
        
        $this->shippingService = $this->getSippingService($request->service_sub_class);
        
        if ($this->createOrder() && $this->assignRecipient() && $this->getPrimaryLabel()) {
            return $this->order;
        }

        $this->order->forcedelete();
        return null;
    }

    private function getSippingService($service_sub_class)
    {
        return ShippingService::query()->where('service_sub_class', $service_sub_class)->first();
    }

    private function createOrder()
    {
        DB::beginTransaction();
        try {
            $order = Order::create([
                'merchant' => 'HomeDeliveryBr',
                'user_id' => $this->tempOrder['user']['id'],
                'carrier' => 'HERCO',
                'tracking_id' => 'HERCO',
                'customer_reference' => 'HERCO',
                'carrier' => 'HERCO',
                'order_date' => Carbon::now(),
                'sender_first_name' => $this->tempOrder['sender_first_name'],
                'sender_last_name' => $this->tempOrder['sender_last_name'] ?? '',
                'sender_email' => $this->tempOrder['sender_email'],
                'sender_phone' => $this->tempOrder['sender_phone'],
                'sender_country_id' => (int)$this->tempOrder['sender_country_id'],
                'sender_state_id' => $this->getSenderState($this->tempOrder['sender_country_id'], $this->tempOrder['sender_state']),
                'sender_city' => $this->tempOrder['sender_city'],
                'sender_address' => $this->tempOrder['sender_address'],
                'sender_zipcode' => $this->tempOrder['sender_zipcode'],
                'weight' => $this->tempOrder['weight'],
                'length' => $this->tempOrder['length'],
                'height' => $this->tempOrder['height'],
                'width' => $this->tempOrder['width'],
                'measurement_unit' => $this->tempOrder['measurement_unit'],
                'shipping_service_id' => $this->shippingService->id,
                'shipping_service_name' => $this->shippingService->name,
                'status' => Order::STATUS_ORDER,
            ]);
            
            if (isset($this->tempOrder['items'])) {

                $totalValue = 0;

                foreach ($this->tempOrder['items'] as $item) {
                    $order->items()->create([
                        'sh_code' => $item['sh_code'],
                        'description' => $item['description'],
                        'quantity' => $item['quantity'],
                        'value' => $item['value'],
                        'contains_battery' => false,
                        'contains_perfume' => false,
                        'contains_flammable_liquid' => false,
                    ]);

                    $totalValue += ($item['quantity'] * $item['value']);
                }

                $order->update([
                    'order_value' => $totalValue,
                ]);
            }
            
            $this->order = $order;
            DB::commit();
            return true;

        } catch (\Exception $ex) {
            $this->error = $ex->getMessage();
            DB::rollBack();
            return false;
        }
    }

    private function assignRecipient()
    {
        $order = $this->order->refresh();
        $order->update([
            'warehouse_number' => $order->getTempWhrNumber()
        ]);

        DB::transaction(function () use ($order) {
            try {

                $order->recipient()->create([
                    'first_name' => $this->tempOrder['recipient']['first_name'],
                    'last_name' => $this->tempOrder['recipient']['last_name'],
                    'phone' => $this->tempOrder['recipient']['phone'],
                    'email' => $this->tempOrder['recipient']['email'],
                    'country_id' => $this->tempOrder['recipient']['country_id'],
                    'state_id' => $this->tempOrder['recipient']['state_id'],
                    'address' => $this->tempOrder['recipient']['address'],
                    'city' => $this->tempOrder['recipient']['city'],
                    'zipcode' => $this->tempOrder['recipient']['zipcode'],
                    'account_type' => $this->tempOrder['recipient']['account_type'],
                ]);
               
            } catch (\Exception $ex) {
                $this->error = $ex->getMessage();
                return false;
            }

            return true;
        });

        return true;
        
    }

    private function getPrimaryLabel()
    {
        $request = $this->createRequest();
        $request->merge([
            'service' => $this->order->shippingService->service_sub_class,
            'sender_state' => $this->tempOrder['sender_state'],
            'pobox_number' => optional(optional($this->order)->user)->pobox_number,
        ]);

        if ($this->order->shippingService->service_sub_class == ShippingService::UPS_GROUND) {
            if ($this->tempOrder['to_herco'] && !$this->upsLabelRepository->getPrimaryLabelForSender($this->order, $request)) {
                $this->error = $this->upsLabelRepository->getUPSErrors();
                return false;
            }

            if ($this->tempOrder['from_herco'] && !$this->upsLabelRepository->getPrimaryLabelForRecipient($this->order)) {
                $this->error = $this->upsLabelRepository->getUPSErrors();
                return false;
            }
        }

        if ($this->order->shippingService->service_sub_class == ShippingService::USPS_PRIORITY || 
            $this->order->shippingService->service_sub_class == ShippingService::USPS_FIRSTCLASS ||
            $this->order->shippingService->service_sub_class == ShippingService::USPS_PRIORITY_INTERNATIONAL ||
            $this->order->shippingService->service_sub_class == ShippingService::USPS_FIRSTCLASS_INTERNATIONAL ||
            $this->order->shippingService->service_sub_class == ShippingService::USPS_GROUND) 
        {
            if ($this->tempOrder['to_herco'] && !$this->uspsLabelRepository->getPrimaryLabelForSender($this->order, $request)) {
                $this->error = $this->uspsLabelRepository->getUSPSErrors();
                return false;
            }

            if (($this->tempOrder['from_herco'] || $this->tempOrder['to_international']) && !$this->uspsLabelRepository->getPrimaryLabelForRecipient($this->order)) {
                $this->error = $this->uspsLabelRepository->getUSPSErrors();
                return false;
            }
        }

        if ($this->order->shippingService->service_sub_class == ShippingService::FEDEX_GROUND) {
            if ($this->tempOrder['to_herco'] && !$this->fedExLabelRepository->getPrimaryLabelForSender($this->order, $request)) {
                $this->error = $this->fedExLabelRepository->getFedExErrors();
                return false;
            }

            if ($this->tempOrder['from_herco'] && !$this->fedExLabelRepository->getPrimaryLabelForRecipient($this->order)) {
                $this->error = $this->fedExLabelRepository->getFedExErrors();
                return false;
            }
        }

        $order = $this->order->refresh();
        chargeAmount($order->gross_total, $order);
        $this->createInvoice($order);

        return true;
        
    }

    private function createInvoice($order)
    {
        $invoice = PaymentInvoice::create([
            'uuid' => PaymentInvoice::generateUUID(),
            'paid_by' => $order->user->id,
            'is_paid' => 1,
            'order_count' => 1,
            'type' => PaymentInvoice::TYPE_PREPAID
        ]);


        $invoice->orders()->sync($order->id);

        $invoice->update([
            'total_amount' => $invoice->orders()->sum('gross_total')
        ]);

        $order->update([
            'is_paid' => true,
        ]);

        return true;
    }

    private function getSenderState($country_id, $state_code)
    {
        $state = State::query()->where([
            ['country_id', $country_id],
            ['code', $state_code]
        ])->first();

        return $state ? $state->id : null;
    }

    private function createRecipient()
    {
        $recipient = new Recipient();
        $recipient->first_name = ($this->request->filled('recipient_first_name')) ? $this->request->recipient_first_name : 'Marcio';
        $recipient->last_name = ($this->request->filled('recipient_first_name')) ? $this->request->recipient_last_name :'Fertias';
        $recipient->phone = ($this->request->filled('recipient_phone')) ? $this->request->recipient_phone : '+13058885191';
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
        $order->sender_country_id = (int)$this->request->origin_country;
        $order->sender_first_name = $order->user->name;
        $order->sender_last_name = $order->user->last_name ?? '';
        $order->sender_email = $order->user->email;
        $order->sender_phone = $order->user->phone;
        $order->pobox_number = $order->user->pobox_number;
        $order->sender_city = $this->request->sender_city;
        $order->sender_state = $this->request->sender_state;
        $order->sender_state_id = State::where([['country_id', Country::US],['code', $this->request->sender_state]])->first()->id;
        $order->sender_address = $this->request->sender_address;
        $order->sender_zipcode = $this->request->sender_zipcode;
        $order->order_date = Carbon::now();
        $order->width = $this->request->width;
        $order->height = $this->request->height;
        $order->length = $this->request->length;
        $order->weight = $this->request->weight;
        $order->measurement_unit = $this->request->unit;
        $order->recipient = $this->recipient;
        $order->to_herco = ($this->request->has('to_herco')) ? true : false;
        $order->from_herco = ($this->request->has('from_herco')) ? true : false;
        $order->to_international = ($this->request->has('to_international')) ? true : false;

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
                    $shippingService->service_sub_class == ShippingService::USPS_FIRSTCLASS_INTERNATIONAL ||
                    $shippingService->service_sub_class == ShippingService::USPS_GROUND);
        });
        
        if ($uspsServices->isEmpty()) {
            return false;
        }

        $request = ($this->request->has('to_herco') || $this->request->has('to_international')) ? $this->createRequest() : null;

        foreach ($uspsServices as $service) {
            
            if ($this->request->has('to_herco')) {
                $request->merge(['service' => $service->service_sub_class]);
            }

            $uspsResponse = ($this->request->has('to_herco')) ? USPSFacade::getSenderRates($this->order, $request) 
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
            $upsResponse = ($this->request->has('to_herco')) ? UPSFacade::getSenderRates($this->order, $request) 
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

    private function getFedExRates()
    {
        $fedExServices = $this->shippingServices->filter(function ($shippingService) {
            return $shippingService->service_sub_class == ShippingService::FEDEX_GROUND;
        });

        if ($fedExServices->isEmpty()) {
            return false;
        }

        $request = ($this->request->has('to_herco')) ? $this->createRequest() : null;

        foreach ($fedExServices as $service) {

            if ($this->request->has('to_herco')) {
                $request->merge(['service' => $service->service_sub_class]);
            }

            $fedExResponse = ($this->request->has('to_herco')) ? FedExFacade::getSenderRates($this->order, $request) : FedExFacade::getRecipientRates($this->order, $service->service_sub_class);
            
            if ($fedExResponse->success == true) {
                array_push($this->shippingRates , ['name'=> $service->name , 'service_sub_class' => $service->service_sub_class, 'rate'=> number_format($fedExResponse->data['output']['rateReplyDetails'][0]['ratedShipmentDetails'][0]['totalNetFedExCharge'], 2)]);
            }else{
                $this->error = $fedExResponse->error['response']['errors'][0]['message'] ?? 'server error, could not get rates';
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
