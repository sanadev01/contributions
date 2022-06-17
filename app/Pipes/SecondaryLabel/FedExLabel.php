<?php

namespace App\Pipes\SecondaryLabel;

use Closure;
use App\Models\User;
use App\Facades\FedExFacade;
use App\Errors\SecondaryLabelError;
use Illuminate\Support\Facades\Cache;
use App\Traits\UpdateOrderForSecondaryLabel;

class FedExLabel
{
    use UpdateOrderForSecondaryLabel;

    public $error;

    private $pickupResponse;
    private $totalFedExCost;
    private $userApiProfit;
    private $totalAmountWithProfit;
    
    public function handle($order, Closure $next)
    {
        if (request()->pickupShipment && !$this->setPickupShipment()) {
            return new SecondaryLabelError($this->error);
        }

        $response = FedExFacade::createShipmentForSender($order, request());
        
        if ($response->success == true) {

            $this->totalFedExCost = $response->data['output']['transactionShipments'][0]['pieceResponses'][0]['baseRateAmount'];

            (request()->exists('consolidated_order')) ? $this->addProfitForConslidatedOrder($order['user']) : $this->addProfit($order->user);

            $trackingNumber = $response->data['output']['transactionShipments'][0]['pieceResponses'][0]['trackingNumber'];
            $apiCost = $this->totalFedExCost;
            $amountToCharge = $this->totalAmountWithProfit;
            $service = request()->service;

            if (request()->exists('consolidated_order')) {
                foreach (request()->orders as $order) {
                    $this->updateOrder($order, $response->data, $trackingNumber, $apiCost, $amountToCharge, $service, $this->pickupResponse);
                }

                $description = 'Bought FedEx Label For : '.$this->getOrderIds(request()->orders);
                $order = request()->orders->first();
            }else{

                $this->updateOrder($order, $response->data, $trackingNumber, $apiCost, $amountToCharge, $service, $this->pickupResponse);

                $description = 'Bought FedEx Label For Order : '.$order->warehouse_number;
            }

            chargeAmount($amountToCharge, $order, $description);

            return $next($order);
        }

        $this->error = $response->error['errors'][0]['message'] ?? 'Unknown error' ;
        return new SecondaryLabelError($this->error);
    }

    private function setPickupShipment()
    {
        $pickupShipmentresponse = FedExFacade::createPickupShipment(request());

        if ($pickupShipmentresponse->success == false) {
            $this->error = $pickupShipmentresponse->error['errors'][0]['message'] ?? 'Pickup shipment not available';

            if ($this->error != 'A pickup already exists.') {
                return false;
            }
        }

        if ($pickupShipmentresponse->success == true) {
            $this->pickupResponse = $pickupShipmentresponse->data;
        }

        return true;
    }

    private function addProfit($user)
    {
        $this->userApiProfit = setting('fedex_profit', null, $user->id);

        if($this->userApiProfit == null || $this->userApiProfit == 0)
        {
            $this->userApiProfit = setting('fedex_profit', null, User::ROLE_ADMIN);
        }

        $profit = $this->totalFedExCost * ($this->userApiProfit / 100);

        $this->totalAmountWithProfit = $this->totalFedExCost + $profit;

        $this->totalAmountWithProfit = round($this->totalAmountWithProfit, 2);
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