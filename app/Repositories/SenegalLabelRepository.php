<?php
namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderTracking;
use App\Services\Senegal\CN23LabelMaker;

class SenegalLabelRepository
{
    private $order;
    private $error;

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
        if ($this->order->api_response == null) {
            return $this->getPrimaryLabel();
        }
        $this->printCN23();
        return true;
    }

    public function updateLabel()
    {
        $code = optional(optional(optional($this->order)->recipient)->country)->code ?? 'SN';
        $this->order->update([
            'api_response' => null,
            'corrios_tracking_code' => 'HD'.date('d').date('m').substr(date('s'), 1, 1).$this->order->id.$code,
        ]); 
        $this->printCN23();        
        return true; 
    }

    public function getError()
    {
        return $this->error;
    }

    private function getPrimaryLabel()
    {
            if(!$this->order->corrios_tracking_code){ 
                $code = optional(optional(optional($this->order)->recipient)->country)->code ?? 'SN';
                $this->order->update([
                    'api_response' => null,
                    'corrios_tracking_code' => 'HD'.date('d').date('m').substr(date('s'), 1, 1).$this->order->id.$code,
                ]);
            }
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
        $this->addOrderTracking();

        $labelPrinter = new CN23LabelMaker();
        $labelPrinter->setOrder($this->order);
        $labelPrinter->saveAs(storage_path("app/labels/{$this->order->corrios_tracking_code}.pdf"));
    }
}
