<?php

namespace App\Repositories\Calculator;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\State;
use App\Models\Country;
use App\Models\Recipient;
use App\Facades\UPSFacade;
use App\Facades\USPSFacade;
use Illuminate\Http\Request;
use App\Models\PaymentInvoice;
use App\Models\ShippingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UPSLabelRepository;
use App\Services\UPS\UPSShippingService;
use App\Services\USPS\USPSShippingService;
use App\Services\Calculators\WeightCalculator;
use Illuminate\Support\Facades\Log;

class USCalculatorRepository
{
    protected $request;
    protected $recipient;
    public $order;
    public $uspsProfit;
    public $upsProfit;
    public $uspsShippingRates = [];
    public $uspsRatesWithProfit = [];
    public $upsShippingRates = [];
    public $upsRatesWithProfit = [];
    public $error;
    protected $chargableWeight;
    public $userLoggedIn = false;

    protected $tempOrder;
    protected $shippingService;

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

    public function setUserUPSProfit()
    {
        if (Auth::check()) 
        {
            $this->upsProfit = setting('ups_profit', null, auth()->user()->id);
            $this->userLoggedIn = true;
        }

        if($this->upsProfit == null || $this->upsProfit == 0)
        {
            $this->upsProfit = setting('ups_profit', null, User::ROLE_ADMIN);
        }

        return $this->upsProfit;
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
            $uspsResponse = USPSFacade::getSenderPrice($order, $requestBody);

            if ($uspsResponse->success == true) {
                array_push($this->uspsShippingRates , ['name'=> $shippingService->name , 'rate'=> number_format($uspsResponse->data['total_amount'], 2)]);
            }else
            {
                $this->error = $uspsResponse->message;
            }
        }

        return $this->uspsShippingRates;
    }

    public function getUPSRates($upsShippingServices, $order)
    {
        if ($upsShippingServices->isEmpty()) {
            return $this->upsShippingRates;
        }

        $request = $this->createRequest($order);

        foreach ($upsShippingServices as $shippingService) {
            $requestBody = $this->mergeShippingServiceIntoRequest($request, $shippingService->service_sub_class);
            $upsResponse = UPSFacade::getSenderPrice($order, $requestBody);
            
            if($upsResponse->success == true)
            {
                array_push($this->upsShippingRates , ['name'=> $shippingService->name , 'service_sub_class' => $shippingService->service_sub_class, 'rate'=> number_format($upsResponse->data['RateResponse']['RatedShipment']['TotalCharges']['MonetaryValue'], 2)]);
            }else
            {
                $this->error = $upsResponse->error['response']['errors'][0]['message'] ?? 'Unknown Error';
            }
        }

        return $this->upsShippingRates;
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

    public function getUPSRatesWithProfit()
    {
        $this->upsRatesWithProfit = [];

        if (!$this->upsShippingRates) {
            return $this->upsRatesWithProfit;
        }

        foreach($this->upsShippingRates as $upsRate)
        {
            $profit = $upsRate['rate'] * ($this->upsProfit / 100);

            $rate = $upsRate['rate'] + $profit;

            array_push($this->upsRatesWithProfit, ['name'=> $upsRate['name'], 'service_sub_class' => $upsRate['service_sub_class'], 'rate'=> number_format($rate, 2)]);
        }

        return $this->upsRatesWithProfit;
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

    public function getUPSShippingServices($order)
    {
        $shippingServices = collect() ;

        $upsShippingService = new UPSShippingService($order);
        foreach (ShippingService::query()->active()->get() as $shippingService) {
            if ( $upsShippingService->isAvailableFor($shippingService) ){
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

    public function getError()
    {
        return $this->error;
    }

    private function createRecipient()
    {
        $recipient = new Recipient();
        $recipient->first_name = 'Marcio';
        $recipient->last_name = 'Fertias';
        $recipient->phone = '+13058885191';
        $recipient->email = 'homedelivery@homedeliverybr.com';
        $recipient->country_id = Country::US;
        $recipient->state_id = State::FL;
        $recipient->address = '2200 NW 129TH AVE';
        $recipient->city = 'Miami';
        $recipient->zipcode = '33182';
        $recipient->account_type = 'individual';

        $this->recipient = $recipient;
    }

    private function createTemporaryOrder()
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

    public function execute($request)
    {
        $this->tempOrder = $request->temp_order;
        $this->shippingService = $this->getSippingService($request->service_sub_class);

        if($this->createOrderForSender() && $this->assignRecipient() && $this->getPrimaryLabel())
        {
           return $this->order;
        }
        
        return false;
    }

    private function getSippingService($service_sub_class)
    {
        return ShippingService::query()->where('service_sub_class', $service_sub_class)->first();
    }

    private function createOrderForSender()
    {
        DB::transaction(function () {
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
                    'sender_last_name' => $this->tempOrder['sender_last_name'],
                    'sender_email' => $this->tempOrder['sender_email'],
                    'sender_phone' => $this->tempOrder['sender_phone'],
                    'sender_country_id' => $this->tempOrder['sender_country_id'],
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

                $this->order = $order;
                return true;

            } catch (\Exception $ex) {
                $this->error = $ex->getMessage();
                return false;
            }
        });

        return true;
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
                Log::info('Recipient Error '.$this->error);
                return false;
            }

            return true;
        });

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

    private function getPrimaryLabel()
    {
        $request = $this->createRequest($this->order);
        $request->merge([
            'service' => $this->order->shippingService->service_sub_class,
            'sender_state' => $this->tempOrder['sender_state'],
        ]);

        if ($this->order->shippingService->service_sub_class == ShippingService::UPS_GROUND) {

            $upsLabelRepository = new UPSLabelRepository();
            if($upsLabelRepository->getPrimaryLabelForSender($this->order, $request))
            {
                $order = $this->order->refresh();
                chargeAmount($order->gross_total, $order);

                $this->createInvoice($order);

                return true;
            }

            $this->error = $upsLabelRepository->getUPSErrors();

            return false;
        }
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
}
