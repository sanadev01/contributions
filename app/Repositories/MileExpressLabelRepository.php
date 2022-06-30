<?php

namespace App\Repositories;

use App\Facades\MileExpressFacade;

class MileExpressLabelRepository
{
    private $order;

    public function handle($order)
    {
        $this->order = $order;

        if ($this->order->api_response == null) {
            $this->getPrimaryLabel();
        }
    }

    private function getPrimaryLabel()
    {
        $response = MileExpressFacade::createShipment($this->order);
    }
}
