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


class USPSCalculatorRepository
{
    private $order;
    protected $usps_errors;
    public $user_api_profit;
    public $total_amount;
    public $service;
    public $usps_cost;
    public $request_order;

    public function handle($request)
    {
        $this->request_order = json_decode($request->order);
        $this->service = $request->service;
        $this->usps_cost = $request->usps_cost;

        if( $this->usps_cost > getBalance())
        {
            $this->usps_errors = 'Not Enough Balance. Please Recharge your account.';

            return false;
        }
        
        $this->createOrder();
        if($this->usps_errors == null)
        {
            $this->createOrderRecipient($this->order);
        }

        $this->buy_USPSLabel($this->order, $request);
    }

    public function getUSPSErrors()
    {
        return $this->usps_errors;
    }

    private function createOrder()
    {
        DB::beginTransaction();

        try {
            $order = Order::create([
                'merchant' => 'HomeDeliveryBr',
                'user_id' => Auth::user()->id,
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
                'status' => Order::STATUS_PREALERT_READY,
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
        $response = USPSFacade::buyLabel($order, $request);
        
        if($response->success == true)
        {
            // storing response in orders table
            $order->update([
                'api_response' => json_encode($response->data),
                'corrios_tracking_code' => $response->data['usps']['tracking_numbers'][0],
                'total' => $this->usps_cost,
                'gross_total' => $this->usps_cost,
            ]);

            $this->chargeAmount($this->usps_cost, $order);

            $this->printBuyUSPSLabel($order);

        } else {

            $this->usps_errors = $response->message;
            return null;
        }
    }

    private function chargeAmount($usps_cost, $order)
    {
        $deposit = Deposit::create([
            'uuid' => PaymentInvoice::generateUUID('DP-'),
            'amount' => $usps_cost,
            'user_id' => Auth::id(),
            'order_id' => $order->id,
            'balance' => Deposit::getCurrentBalance() - $usps_cost,
            'is_credit' => false,
            'description' => 'Bought USPS Label For : '.$order->warehouse_number,
        ]);
        
        if ( $order ){
            $order->deposits()->sync($deposit->id);
        }

        return $deposit;
    }

    public function printBuyUSPSLabel(Order $order)
    {
        $labelPrinter = new USPSLabelMaker();
        $labelPrinter->setOrder($order);
        $labelPrinter->saveUSPSLabel();

        return true;
    }

}