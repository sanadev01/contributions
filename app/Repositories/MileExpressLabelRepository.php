<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderTracking;
use App\Facades\MileExpressFacade;
use Illuminate\Support\Facades\Storage;

class MileExpressLabelRepository
{
    private $order;
    private $error;

    public function run(Order $order,$update)
    {
        if($update){
            return $this->updateLabel($order);
        }
        else {
            return $this->handle($order);
        }
    }

    public function handle($order)
    {
        $this->order = $order;

        if ($this->order->api_response == null) {
            return $this->getPrimaryLabel();
        }

        $this->printCN23();
        return true;
    }

    public function updateLabel()
    {
        $this->error = 'Sorry!, Colombia Label can not be updated';
    }

    public function getError()
    {
        return $this->error;
    }

    private function getPrimaryLabel()
    {
        // $response = MileExpressFacade::createShipment($this->order);
        // return dd($response); 
            $this->order->update([
                'api_response' => null,
                'corrios_tracking_code' => 'MIE20230502H7808',
            ]);

            $this->order->refresh();

            $this->addOrderTracking();

            $this->printCN23();

            return true; 
 
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

    private function printCN23()
    {
        
        if (Storage::disk('local')->exists('labels/'.$this->order->corrios_tracking_code.'.pdf')) {
            return true;
        }
        
        $mileExpressShipmentId = json_decode($this->order->api_response)->data->id;
        
        $labelResponse = MileExpressFacade::getLabel($mileExpressShipmentId);

        if ($labelResponse->success == true) {
            Storage::disk('local')->put("labels/{$this->order->corrios_tracking_code}.pdf", $labelResponse->data);
            return true;
        }

        return $this->error = $labelResponse->error;
    }
}
