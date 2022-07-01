<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderTracking;
use App\Facades\MileExpressFacade;

class MileExpressLabelRepository
{
    private $order;
    private $error;

    public function handle($order)
    {
        $this->order = $order;

        if ($this->order->api_response == null) {
            $this->getPrimaryLabel();
        }

        return true;
    }

    public function getError()
    {
        return $this->error;
    }

    private function getPrimaryLabel()
    {
        $response = MileExpressFacade::createShipment($this->order);
        
        if ($response->success == true) {
            $this->order->update([
                'api_response' => json_encode($response->data),
                'corrios_tracking_code' => $response->data['data']['code']
            ]);

            $this->order->refresh();

            $this->addOrderTracking();

            return true;
        }

        $this->error = $response->error ?? 'server error';
        return false;
    }

    private function addOrderTracking()
    {
        if($this->order->trackings->isEmpty())
        {
            OrderTracking::create([
                'order_id' => $this->order->id,
                'status_code' => Order::STATUS_PAYMENT_DONE,
                'type' => 'HD',
                'description' => 'Order Placed',
                'country' => ($this->order->user->country != null) ? $this->order->user->country->code : 'US',
            ]);
        }

        return true;
    }
}
