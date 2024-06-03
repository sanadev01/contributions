<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderTracking;
use App\Facades\ColombiaShippingFacade;
use App\Services\Colombia\ColombiaService;
class ColombiaLabelRepository
{
    protected $error;

    public $order;
    public function __contruct(Order $order)
    {
        $this->order = $order;
    }
    public function run(Order $order,$update)
    {
        $this->order = $order; 
        if($update){
            return $this->updateLabel();
        }
        else {
            return $this->handle();
        }
    }
    
    public function handle()
    { 

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
        $response = (new ColombiaService())->createShipment($this->order);

        if ($response['success'] == true) {

            if (isset($response['data']['byteGuidePDF'])) {

                unset($response['data']['byteGuidePDF']);
            }

            $this->order->update([
                'api_response' => json_encode($response['data']),
                'corrios_tracking_code' => $response['data']['strBarcode'],
                'sinerlog_url_label' => $response['data']['strUrlGuide'],
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
