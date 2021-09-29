<?php


namespace App\Repositories;


use App\Models\User;
use App\Models\Order;
use App\Facades\USPSFacade;
use App\Models\OrderTracking;
use App\Services\USPS\USPSLabelMaker;


class USPSLabelRepository
{
    protected $usps_errors;
    public $user_api_profit;
    public $total_amount;

    public function handle($order)
    {
        if(($order->shipping_service_name == 'Priority' || $order->shipping_service_name == 'FirstClass') && $order->api_response == null)
        {
    
            $this->generat_USPSLabel($order);

        }elseif($order->api_response != null)
        {
            
            $this->printLabel($order);
        }
    }

    public function update($order)
    {
        $this->generat_USPSLabel($order);
    }

    public function generat_USPSLabel($order)
    {
        $response = USPSFacade::generateLabel($order);

        if($response->success == true)
        {
            //storing response in orders table
            $order->update([
                'api_response' => json_encode($response->data),
                'corrios_tracking_code' => $response->data['usps']['tracking_numbers'][0],
            ]);
            // store order status in order tracking
            $this->addOrderTracking($order);

            $this->printLabel($order);

        } else {

            $this->usps_errors = $response->message;
            return null;
        }
        
    }

    public function printLabel(Order $order)
    {
        $labelPrinter = new USPSLabelMaker();
        $labelPrinter->setOrder($order);
        $labelPrinter->saveLabel();

        return true;
    }

    public function getUSPSErrors()
    {
        return $this->usps_errors;
    }

    public function addOrderTracking($order)
    {
        if($order->trackings->isEmpty())
        {
            OrderTracking::create([
                'order_id' => $order->id,
                'status_code' => Order::STATUS_PAYMENT_DONE,
                'type' => 'HD',
                'description' => 'Order Placed',
                'country' => ($order->user->country != null) ? $order->user->country->code : 'US',
            ]);
        }    

        return true;
    }
    
    public function getRates($request)
    {
        $order = Order::find($request->order_id);
        $response = USPSFacade::getSenderPrice($order, $request);

        if($response->success == true)
        {
            $this->addProfit($order->user, $response->data['total_amount']);

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

    private function addProfit($user, $usps_rate)
    {
        $this->user_api_profit = $user->api_profit;

        if($this->user_api_profit == 0)
        {
            $admin = User::where('role_id',1)->first();

            $this->user_api_profit = $admin->api_profit;
        }

        $profit = $usps_rate * ($this->user_api_profit / 100);

        $this->total_amount = $usps_rate + $profit;

        return true;
    }

    public function buyLabel($request, $order)
    {
        
        if ( $request->total_price > getBalance())
        {
            $this->usps_errors = 'Not Enough Balance. Please Recharge your account.';

            return false;
        }

        if($order->corrios_usps_tracking_code != null)
        {
            $this->usps_errors = 'Label has already been generated';

            return false;
        }

        $this->buy_USPSLabel($order, $request);

        return true;
    }

    private function buy_USPSLabel($order, $request)
    {
        $response = USPSFacade::buyLabel($order, $request);
        
        if($response->success == true)
        {
            // storing response in orders table
            $order->update([
                'corrios_usps_tracking_code' => $response->data['usps']['tracking_numbers'][0],
                'usps_cost' => $request->total_price,
            ]);

            chargeAmount($request->total_price, $order);

            return true;

        } else {

            $this->usps_errors = $response->message;
            return null;
        }
    }
    
}