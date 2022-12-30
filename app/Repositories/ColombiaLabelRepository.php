<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderTracking;
use App\Facades\ColombiaShippingFacade;

class ColombiaLabelRepository
{
    protected $error;

    public function handle($order)
    {
        $this->order = $order;

        if(!$this->order->api_response){
            $this->getPrimaryLabel();

            return;
        }

        return;
    }

    public function updateLabel()
    {
        $this->error = 'Sorry!, This Label can not be updated';
    }

    public function getError()
    {
        return $this->error;
    }

    private function getPrimaryLabel()
    {
        $response = ColombiaShippingFacade::createShipment($this->order);

        if ($response['success'] == true) {

            if (isset($response['data']['byteGuidePDF'])) {

                unset($response['data']['byteGuidePDF']);
            }

            $this->order->update([
                'api_response' => json_encode($response['data']),
                'corrios_tracking_code' => $response['data']['strBarcode'],
            ]);
            
            $this->addOrderTracking();

            return true;
        }

        if ($response['success'] == false) {
            $this->error = $response['error'];

            return false;
        }
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
