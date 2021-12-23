<?php


namespace App\Repositories;


use App\Models\User;
use App\Models\Order;
use App\Models\Deposit;
use App\Facades\USPSFacade;
use App\Models\OrderTracking;
use App\Models\PaymentInvoice;
use App\Models\ShippingService;
use Illuminate\Support\Facades\Auth;
use App\Services\USPS\USPSLabelMaker;
use App\Services\USPS\USPSShippingService;


class USPSBulkLabelRepository
{
    private $total_weight = 0;
    private $temp_weight = 0;
    private $calculated_width = 0;
    private $calculated_height = 0;
    private $calculated_length = 0;
    private $orderWithMaxWeight;
    private $orders;
    public $usps_errors;
    public $user_api_profit;
    public $total_amount;

    public function handle($order_Ids)
    {
        $this->getOrdersWeight($order_Ids);
        $this->calculateOrderDimensions();
        $order = $this->createOrder();

        return $order;
    }

    public function getRates($order, $request)
    {
        
        $response = USPSFacade::getSenderPrice($order, $request);

        if($response->success == true)
        {
            $this->addProfit($response->data['total_amount']);

            return (Array)[
                'success' => true,
                'total_amount' => round($this->total_amount, 2),
            ]; 
        }

        return (Array)[
            'success' => false,
            'message' => 'server error, could not get rates',
        ]; 
    }

    public function generateLabel($order, $usps_cost, $request)
    {
        $response = USPSFacade::buyLabel($order, $request);
        if($response->success == true)
        {
            // storing response in orders table
            foreach($this->orders as $order)
            {
                $order->update([
                    'us_api_response' => json_encode($response->data),
                    'us_api_tracking_code' => $response->data['usps']['tracking_numbers'][0],
                    'us_api_cost' => $usps_cost,
                ]);

                $order->refresh();
            }
            
            $this->chargeAmount($usps_cost);
            $this->printLabel($this->getFirstOrder());

            return true;

        } else {

            $this->usps_errors = $response->message;
            return false;
        }
    }
    
    public function printLabel(Order $order)
    {
        $labelPrinter = new USPSLabelMaker();
        $labelPrinter->setOrder($order);
        $labelPrinter->saveUSPSLabel();

        return true;
    }

    public function getUSPSErrors()
    {
        return $this->usps_errors;
    }

    private function getOrdersWeight($order_Ids)
    {
        $this->orders = Order::whereIn('id', $order_Ids)->get();
        
        foreach ($this->orders as $order) {
            $this->total_weight += $order->getWeight('kg');

            if($order->getWeight('kg') > $this->temp_weight)
            {
                $this->temp_weight = $order->getWeight('kg');
                $this->orderWithMaxWeight = $order;
            }
            
        }
        return;
    }

    private function calculateOrderDimensions()
    {
        $width = $this->orderWithMaxWeight->width;
        $height = $this->orderWithMaxWeight->height;
        $length = $this->orderWithMaxWeight->length;

        $this->label_dimension = auth()->user()->order_dimension;

        if($this->label_dimension == 0)
        {
            $admin = User::where('role_id',1)->first();

            $this->label_dimension = $admin->order_dimension;
        }

        $increased_width = $width * ($this->label_dimension / 100);
        $this->calculated_width = $width + $increased_width;

        $increased_height = $height * ($this->label_dimension / 100);
        $this->calculated_height = $height + $increased_height;

        $increased_length = $length * ($this->label_dimension / 100);
        $this->calculated_length = $length + $increased_length;

        return true;


    }

    private function createOrder()
    {
        $order = new Order();
        $order->id = 1;
        $order->user = Auth::user();
        $order->sender_country_id = 250;
        $order->weight = $this->total_weight;
        $order->width = $this->calculated_width;
        $order->height = $this->calculated_height;
        $order->length = $this->calculated_length;
        $order->measurement_unit = $this->orderWithMaxWeight->measurement_unit;
        
        return $order;
    }

    private function addProfit($usps_rate)
    {
        $this->user_api_profit = auth()->user()->api_profit;

        if($this->user_api_profit == 0)
        {
            $admin = User::where('role_id',1)->first();

            $this->user_api_profit = $admin->api_profit;
        }

        $profit = $usps_rate * ($this->user_api_profit / 100);

        $this->total_amount = $usps_rate + $profit;

        return true;
    }
    
    private function chargeAmount($usps_cost)
    {
        $order = $this->getFirstOrder();

        $deposit = Deposit::create([
            'uuid' => PaymentInvoice::generateUUID('DP-'),
            'amount' => $usps_cost,
            'user_id' => Auth::id(),
            'order_id' => $order->id,
            'balance' => Deposit::getCurrentBalance() - $usps_cost,
            'is_credit' => false,
            'description' => $this->getDescription(),
        ]);

        if ( $order ){
            $order->deposits()->sync($deposit->id);
        }

        return $deposit;
    }

    public function getFirstOrder()
    {
        $order = $this->orders->first();

        return $order;
    }

    private function getDescription()
    {
        $description = 'Bought USPS Label for : ';

        foreach($this->orders as $order)
        {
            $description .= $order->warehouse_number . ',';
        }

        return $description;
    }

}