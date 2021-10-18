<?php


namespace App\Repositories;


use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Deposit;
use App\Facades\USPSFacade;
use App\Models\OrderTracking;
use App\Models\PaymentInvoice;
use App\Models\ShippingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\USPS\USPSLabelMaker;
use App\Services\USPS\USPSShippingService;
use PhpParser\Node\Expr\Cast\Object_;

class USPSCalculatorRepository
{
    private $order;
    private $user;
    protected $usps_errors;
    public $user_api_profit;
    public $total_amount;
    public $service;
    public $usps_cost;
    public $request_order;

    public function handle($request)
    {
        $this->request_order = json_decode($request->order);
        $this->getUser($request->user_id);
        $this->service = $request->service;
        $this->usps_cost = $request->usps_cost;
        
        $current_balance =  $this->checkBalance();
        if( $this->usps_cost > $current_balance)
        {
            $this->usps_errors = 'Not Enough Balance. Please Recharge your account.';

            return false;
        }
        
        $this->createOrder();
        if($this->usps_errors == null)
        {
            $this->createOrderRecipient($this->order);
        }

        $this->buy_USPSLabel($this->order, $this->request_order);

        return $this->order;
    }

    public function getUSPSErrors()
    {
        return $this->usps_errors;
    }

    private function getUser($user_id)
    {
        $this->user = User::where('id', $user_id)->first();
    }

    private function checkBalance()
    {
        $lastTransaction = Deposit::query()->where('user_id', $this->user->id)->latest('id')->first();
        if ( !$lastTransaction ){
            return 0;
        }

        return $lastTransaction->balance;
    }

    private function createOrder()
    {
        DB::beginTransaction();

        try {
            $order = Order::create([
                'merchant' => 'HomeDeliveryBr',
                'user_id' => $this->user->id,
                'carrier' => 'HERCO',
                'tracking_id' => 'HERCO',
                'customer_reference' => 'HERCO',
                'carrier' => 'HERCO',
                'order_date' => Carbon::now(),
                'sender_first_name' => $this->request_order->sender_first_name,
                'sender_last_name' => $this->request_order->sender_last_name,
                'sender_country_id' => $this->request_order->sender_country_id,
                'weight' => $this->request_order->weight,
                'length' => $this->request_order->length,
                'width' => $this->request_order->width,
                'height' => $this->request_order->height,
                'measurement_unit' => $this->request_order->measurement_unit,
                'shipping_service_id' => $this->getShippingService($this->service),
                'shipping_service_name' => $this->service,
                'status' => Order::STATUS_ORDER,
            ]);
            
            DB::commit();

            $order->refresh();
    
            $order->update([
                'warehouse_number' => $order->getTempWhrNumber()
            ]);
    
            $this->order = $order;
            return true;

        } catch (\Exception $ex) {

            DB::rollback();
            $this->usps_errors = $ex->getMessage();
            return false;
        }
        
        
    }

    private function getShippingService($service_name)
    {
        $shipping_service = ShippingService::where('name', $service_name)->first();

        return $shipping_service->id;
    }

    private function createOrderRecipient()
    {
        DB::beginTransaction();

        try {
            $this->order->recipient()->create([
                'first_name' => 'HERCO',
                'last_name' => 'HomedeliveryBr',
                'email' => 'homedelivery@homedeliverybr.com',
                'phone' => '+13058885191',
                'city' => 'Miami',
                'street_no' => '2200',
                'address' => '2200 NW 129TH AVE',
                'zipcode' => '33182',
                'state_id' => 4622,
                'country_id' => 250,
                'account_type' => 'individual',
            ]);

            DB::commit();
            return true;

        } catch (\Exception $ex) {
            DB::rollback();
            $this->usps_errors = $ex->getMessage();
            return false;
        }
    }

    private function buy_USPSLabel($order, $request)
    {
        $request_sender_data = $this->make_request_data($request);
        $response = USPSFacade::buyLabel($order, $request_sender_data);
        
        if($response->success == true)
        {
            // storing response in orders table
            $order->update([
                'api_response' => json_encode($response->data),
                'corrios_tracking_code' => $response->data['usps']['tracking_numbers'][0],
                'is_invoice_created' => true,
                'is_shipment_added' => true,
                'user_declared_freight' => $response->data['total_amount'],
                'shipping_value' => $this->usps_cost,
                'total' => $this->usps_cost,
                'gross_total' => $this->usps_cost,
                'status' => Order::STATUS_PAYMENT_DONE,
            ]);

            $this->chargeAmount($this->usps_cost, $order);
            $this->createInvoivce($order);
            $this->printLabel($order);

            return true;

        } else {

            $this->usps_errors = $response->message;
            return null;
        }
    }

    private function make_request_data($request)
    {
        $data = (Object)[
            'sender_country_id' => $request->sender_country_id,
            'first_name' => $request->sender_first_name,
            'last_name' => $request->sender_last_name,
            'pobox_number' => $request->pobox_number,
            'sender_state' => $request->sender_state,
            'sender_city' => $request->sender_city,
            'sender_address' => $request->sender_address,
            'sender_zipcode' => $request->sender_zipcode,
            'service' => $this->service,
        ];

        return $data;
    }

    private function chargeAmount($usps_cost, $order)
    {
        $deposit = Deposit::create([
            'uuid' => PaymentInvoice::generateUUID('DP-'),
            'amount' => $usps_cost,
            'user_id' => $this->user->id,
            'order_id' => $order->id,
            'balance' => $this->checkBalance() - $usps_cost,
            'is_credit' => false,
            'description' => 'Bought USPS Label For : '.$order->warehouse_number,
        ]);
        
        if ( $order ){
            $order->deposits()->sync($deposit->id);
        }

        return $deposit;
    }

    private function createInvoivce($order)
    {
        $invoice = PaymentInvoice::create([
            'uuid' => PaymentInvoice::generateUUID(),
            'paid_by' => $this->user->id,
            'is_paid' => 1,
            'order_count' => 1,
            'type' => PaymentInvoice::TYPE_PREPAID
        ]);


        $invoice->orders()->sync($order->id);

        $invoice->update([
            'total_amount' => $invoice->orders()->sum('gross_total')
        ]);

        return true;
    }

    public function printLabel(Order $order)
    {
        $labelPrinter = new USPSLabelMaker();
        $labelPrinter->setOrder($order);
        $labelPrinter->saveLabel();

        return true;
    }

}