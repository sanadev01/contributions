<?php

namespace App\Pipes\SecondaryLabel;

use Closure;
use App\Models\User;
use App\Facades\UPSFacade;
use App\Errors\SecondaryLabelError;
use App\Traits\UpdateOrderForSecondaryLabel;

class UPSLabel
{
    use UpdateOrderForSecondaryLabel;

    public $error;

    private $totalPickupCost = 0;
    private $totalCostWithProfit;
    private $totalUpsCost = 0;
    private $userUpsProfit;
    private $pickupResponse;

    public function handle($order, Closure $next)
    {
        if (request()->pickupShipment && !$this->setPickupShipment($order)) {
            return new SecondaryLabelError($this->error);
        }

        $response = UPSFacade::getLabelForSender($order, request());
        
        if($response->success == true)
        {
            $this->totalUpsCost += $response->data['ShipmentResponse']['ShipmentResults']['ShipmentCharges']['TotalCharges']['MonetaryValue'];
            
            (request()->exists('consolidated_order')) ? $this->addProfitForConslidatedOrder($order['user']) : $this->addProfit($order->user);

            $trackingNumber = $response->data['ShipmentResponse']['ShipmentResults']['ShipmentIdentificationNumber'];
            $apiCost = $this->totalUpsCost + $this->totalPickupCost;
            $amountToCharge = $this->totalCostWithProfit;
            $service = request()->service;

            if (request()->exists('consolidated_order')) {
                
                foreach (request()->orders as $order) {
                    $this->updateOrder($order, $response->data, $trackingNumber, $apiCost, $amountToCharge, $service, $this->pickupResponse);
                }

                $description = 'Bought USPS Label For : '.$this->getOrderIds(request()->orders);
                $order = request()->orders->first();

            }else{

                $this->updateOrder($order, $response->data, $trackingNumber, $apiCost, $amountToCharge, $service, $this->pickupResponse);

                $description = 'Bought UPS Label For Order : '.$order->warehouse_number;
            }

            chargeAmount($amountToCharge, $order, $description);

            return $next($order);
        }

        $this->error = $response->error['response']['errors'][0]['message'] ?? 'Something went wrong with UPS service';
        return new SecondaryLabelError($this->error);
    }

    private function setPickupShipment($order)
    {
        $pickupShipmentresponse = UPSFacade::createPickupShipment($order, request());

        if ($pickupShipmentresponse->success == false) {
            $this->error = $pickupShipmentresponse->error['response']['errors'][0]['message'] ?? 'Unknown Error';
            return false;
        }

        $this->totalPickupCost += $pickupShipmentresponse->data['PickupCreationResponse']['RateResult']['GrandTotalOfAllCharge'];
        $this->pickupResponse = $pickupShipmentresponse->data;
        return true;
    }

    private function addProfit($user)
    {
        $this->userUpsProfit = setting('ups_profit', null, $user->id);

        if($this->userUpsProfit == null || $this->userUpsProfit == 0)
        {
            $this->userUpsProfit = setting('ups_profit', null, User::ROLE_ADMIN);
        }

        $upsRate = $this->totalUpsCost + $this->totalPickupCost;
        $profit = $upsRate * ($this->userUpsProfit / 100);
        $this->totalCostWithProfit += $upsRate + $profit;

        $this->totalCostWithProfit = round($this->totalCostWithProfit, 2);

        return true;
    }

    private function addProfitForConslidatedOrder($user)
    {
        $user = User::find($user['id']);
        return $this->addProfit($user);
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