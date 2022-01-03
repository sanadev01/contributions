<?php


namespace App\Repositories;

use App\Models\Order;
use App\Facades\USPSFacade;
use App\Models\OrderTracking;
use App\Models\ShippingService;
use App\Services\USPS\USPSLabelMaker;
use App\Services\USPS\USPSShippingService;


class USPSLabelRepository
{
    protected $usps_errors;
    public $user_api_profit;
    public $total_amount_with_profit;

    public function handle($order)
    {
        if(($order->shippingService->service_sub_class == ShippingService::USPS_PRIORITY || $order->shippingService->service_sub_class == ShippingService::USPS_FIRSTCLASS) && $order->api_response == null)
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

    public function printBuyUSPSLabel(Order $order)
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
    
    public function getShippingServices($order)
    {
        $shippingServices = collect();

        $usps_shippingService = new USPSShippingService($order);

        foreach (ShippingService::query()->active()->get() as $shippingService) {
            if ( $usps_shippingService->isAvailableFor($shippingService) ){
                $shippingServices->push($shippingService);
            }
        }

        if($shippingServices->isEmpty())
        {
            $this->ups_errors = 'No shipping services available for this order';
        }
        
        if($shippingServices->contains('service_sub_class', ShippingService::USPS_PRIORITY) || $shippingServices->contains('service_sub_class', ShippingService::USPS_FIRSTCLASS))
        {
            if(!setting('usps', null, $order->user->id))
            {
                $this->usps_errors = "USPS is not enabled for your account";
                $shippingServices = collect() ;
            }
        }
        
        return $shippingServices;

        
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
                'total_amount' => round($this->total_amount_with_profit, 2),
            ]; 
        }

        return (Array)[
            'success' => false,
            'message' => 'server error, could not get rates',
        ]; 
    }

    private function addProfit($user, $usps_rate)
    {
        $this->user_api_profit = setting('usps_profit', null, $user->id);

        if($this->user_api_profit == null || $this->user_api_profit == 0)
        {
            $this->user_api_profit = setting('usps_profit', null, 1);
        }

        $profit = $usps_rate * ($this->user_api_profit / 100);

        $this->total_amount_with_profit = $usps_rate + $profit;

        return true;
    }

    public function buyLabel($request, $order)
    {
        if($order->hasSecondLabel())
        {
            $this->printBuyUSPSLabel($order);

            return true;
        }
        
        if ( $request->total_price > getBalance())
        {
            $this->usps_errors = 'Not Enough Balance. Please Recharge your account.';

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
                'us_api_response' => json_encode($response->data),
                'us_api_tracking_code' => $response->data['usps']['tracking_numbers'][0],
                'us_secondary_label_cost' => setUSCosts($response->data['total_amount'], $request->total_price),
                'us_api_service' => $request->service,
            ]);

            chargeAmount($request->total_price, $order, 'Bought USPS Label For : ');

            $this->printBuyUSPSLabel($order);

        } else {

            $this->usps_errors = $response->message;
            return null;
        }
    }
    
}