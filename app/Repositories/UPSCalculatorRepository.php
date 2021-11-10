<?php

namespace App\Repositories;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Deposit;
use App\Facades\UPSFacade;
use App\Models\OrderTracking;
use App\Models\PaymentInvoice;
use App\Models\ShippingService;
use Illuminate\Support\Facades\DB;
use App\Services\UPS\UPSLabelMaker;
use Illuminate\Support\Facades\Auth;

class UPSCalculatorRepository
{
    private $order;
    private $user;
    protected $ups_errors;
    public $user_api_profit;
    public $total_amount;
    public $service;
    public $shipping_service_id;
    public $shipping_service_sub_class;
    public $ups_cost;
    public $request_order;

    public function handle($request)
    {
        $this->request_order = json_decode($request->order);
        $this->getUser($request->user_id);
        $this->service = $request->service;
        $this->getShippingService($this->service);
        $this->ups_cost = $request->ups_cost;
        $current_balance =  $this->checkBalance();
        if( $this->ups_cost > $current_balance)
        {
            $this->ups_errors = 'Not Enough Balance. Please Recharge your account.';

            return false;
        }
        
        $this->createOrder();
        if($this->ups_errors == null)
        {
            $this->createOrderRecipient();
        }

        $this->buy_UPSLabel($this->order, $this->request_order);

        return $this->order;
    }

    public function getUPSErrors()
    {
        return $this->ups_errors;
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
                'shipping_service_id' =>  $this->shipping_service_id,
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
            $this->ups_errors = $ex->getMessage();
            return false;
        }
    }

    private function getShippingService($service_name)
    {
        $shipping_service = ShippingService::where('name', $service_name)->first();

        $this->shipping_service_id = $shipping_service->id;
        $this->shipping_service_sub_class = $shipping_service->service_sub_class;

        return true;
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
            $this->ups_errors = $ex->getMessage();
            return false;
        }
    }

    private function buy_UPSLabel($order)
    {
        $request_sender_data = $this->make_request_data();
        $response = UPSFacade::buyLabel($order, $request_sender_data);
        
        if($response->success == true)
        {
            // storing response in orders table
            $order->update([
                'api_response' => json_encode($response->data),
                'corrios_tracking_code' => $response->data['FreightShipResponse']['ShipmentResults']['ShipmentNumber'],
                'is_invoice_created' => true,
                'is_shipment_added' => true,
                'user_declared_freight' => $response->data['FreightShipResponse']['ShipmentResults']['TotalShipmentCharge']['MonetaryValue'],
                'shipping_value' => $this->ups_cost,
                'total' => $this->ups_cost,
                'gross_total' => $this->ups_cost,
                'status' => Order::STATUS_PAYMENT_DONE,
            ]);

            $this->chargeAmount($this->ups_cost, $order);
            $this->createInvoivce($order);
            $this->printLabel($order);

            return true;

        } else {

            $this->ups_errors = $response->message;
            return null;
        }
    }

    private function make_request_data()
    {
        $data = (Object)[
            'sender_country_id' => $this->request_order->sender_country_id,
            'first_name' => $this->request_order->sender_first_name,
            'last_name' => $this->request_order->sender_last_name,
            'sender_email' => $this->request_order->sender_email,
            'sender_phone' => $this->request_order->sender_phone,
            'pobox_number' => $this->request_order->pobox_number,
            'sender_state' => $this->request_order->sender_state,
            'sender_city' => $this->request_order->sender_city,
            'sender_address' => $this->request_order->sender_address,
            'sender_zipcode' => $this->request_order->sender_zipcode,
            'service' => $this->shipping_service_sub_class,
        ];

        return $data;
    }

    private function chargeAmount($ups_cost, $order)
    {
        $deposit = Deposit::create([
            'uuid' => PaymentInvoice::generateUUID('DP-'),
            'amount' => $ups_cost,
            'user_id' => $this->user->id,
            'order_id' => $order->id,
            'balance' => $this->checkBalance() - $ups_cost,
            'is_credit' => false,
            'description' => 'Bought UPS Label For : '.$order->warehouse_number,
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
        $labelPrinter = new UPSLabelMaker();
        $labelPrinter->setOrder($order);
        $labelPrinter->saveLabel();

        return true;
    }


}