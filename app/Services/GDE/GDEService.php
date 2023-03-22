<?php

namespace App\Services\GDE;

use App\Models\Order;

class GDEService
{
    protected $order;
    protected $tracking_number;

    public function generateLabel($order)
    {
        $this->order = $order;

        if($order->corrios_tracking_code == null && !$order->hasCN23())
        {
        
           $tracking_number = $this->generateTrackingNumber();
           $order->update([
               'corrios_tracking_code' => $tracking_number,
               'api_response' => $tracking_number,
               'cn23' => $tracking_number
            ]);

            $order->refresh();
            
        }

        return $this->printLabel($order);

    }

    private function generateTrackingNumber()
    {
        $random_number = random_int(1000, 9999);
        
        $tracking_number = 'BR'.$this->order->id.$random_number.'US';

        return $tracking_number;
    }

    public function printLabel(Order $order)
    {
        $labelPrinter = new GDELabelMaker();
        $labelPrinter->setOrder($order);
        $labelPrinter->saveAs(storage_path("app/labels/{$order->corrios_tracking_code}.pdf"));

        return true;
    }
}
