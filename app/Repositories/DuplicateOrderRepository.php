<?php

namespace App\Repositories;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class DuplicateOrderRepository extends Model
{
    private $error;

    public function makeDuplicate(Order $order)
    {
        return $this->makeOrderCopy($order);
    }

    private function makeOrderCopy(Order $order)
    {
        $copy = $order->replicate();
        $copy->is_paid = false;
        $copy->corrios_tracking_code = null;
        $copy->order_date = Carbon::now();
        $copy->is_received_from_sender = false;
        $copy->purchase_invoice = null;
        $copy->status = Order::STATUS_ORDER;
        $copy->is_consolidated = false;
        $copy->cn23 = null;
        $copy->save();
        $copy->warehouse_number = $copy->getTempWhrNumber();
        $copy->save();

        $this->makeRecipientCopy($order,$copy);
        $this->makeServicesCopy($order,$copy);
        return $copy;
    }

    private function makeRecipientCopy(Order $order, Order $copy)
    {
        $recipientCopy = $order->recipient->replicate();
        $recipientCopy->order_id = $copy->id;
        $recipientCopy->save();
    }

    private function makeServicesCopy(Order $order, Order $copy)
    {
        foreach ($order->services as $service) {
            $serviceCopy = $service->replicate();
            $serviceCopy->save();
            $copy->services()->save($$serviceCopy);
        }
    }

    public function getError()
    {
        return $this->error;
    }
}
