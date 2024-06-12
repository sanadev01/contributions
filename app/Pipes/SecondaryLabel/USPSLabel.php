<?php

namespace App\Pipes\SecondaryLabel;

use Closure;
use App\Facades\USPSFacade;
use App\Errors\SecondaryLabelError;
use App\Traits\UpdateOrderForSecondaryLabel;

class USPSLabel
{
    use UpdateOrderForSecondaryLabel;
    
    public function handle($order, Closure $next)
    {
        $response = USPSFacade::getLabelForSender($order, request());
        
        if($response->success == true) {
            
            $trackingNumber = $response->data['usps']['tracking_numbers'][0];
            $apiCost = $response->data['total_amount'];
            $amountToCharge = request()->total_price;
            $service = request()->service;

            if (request()->exists('consolidated_order')) {

                foreach (request()->orders as $order) {
                    $this->updateOrder($order, $response->data, $trackingNumber, $apiCost, $amountToCharge, $service);
                }

                $description = 'Bought USPS Label For : '.$this->getOrderIds(request()->orders);

                $order = request()->orders->first();
            }else {

                $this->updateOrder($order, $response->data, $trackingNumber, $apiCost, $amountToCharge, $service);

                $description = 'Bought USPS Label For Order : '.$order->warehouse_number;
            }

            chargeAmount($amountToCharge, $order, $description);

            return $next($order);
        }
        
        return new SecondaryLabelError($response->message);
    }

    private function getOrderIds($orders)
    {
        $warehouse_numbers = [];
        foreach ($orders as $order) {
            $warehouse_numbers[] = $order->warehouse_number;
        }

        return implode(' :,', $warehouse_numbers);
    }
}