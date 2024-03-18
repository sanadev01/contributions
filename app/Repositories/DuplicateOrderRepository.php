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
    
    public function makeDuplicatePreAlert(Order $order)
    {
        return $this->makePreAlertCopy($order);
    }

    private function makeOrderCopy(Order $order)
    {
        $copy = $order->replicate();
        $copy->is_paid = false;
        $copy->corrios_tracking_code = null;
        $copy->sinerlog_url_label = null;
        $copy->api_response = null;
        $copy->us_api_tracking_code = null;
        $copy->us_api_response = null;
        $copy->api_pickup_response = null;
        $copy->us_api_service = null;
        $copy->us_secondary_label_cost = null;
        $copy->sinerlog_tran_id = null;
        $copy->sinerlog_freight = null;
        $copy->order_date = Carbon::now();
        $copy->is_received_from_sender = false;
        $copy->purchase_invoice = null;
        $copy->status = Order::STATUS_ORDER;
        $copy->is_consolidated = false;
        $copy->cn23 = null;
        // $copy->weight_discount = null;
        $copy->save();
        $copy->warehouse_number = $copy->getTempWhrNumber(false);
        $copy->save();

        $this->makeRecipientCopy($order,$copy);
        $this->makeServicesCopy($order,$copy);
        $copy->update([
            'shipping_service_id' => null,
            'user_declared_freight' => 0,
            'total' => 0,
            'gross_total' => 0,
        ]);
        return $copy;
    }
    
    private function makePreAlertCopy(Order $order)
    {
        $copy = $order->replicate();
        $copy->order_date = Carbon::now();
        $copy->save();
        $copy->warehouse_number = $copy->getTempWhrNumber(false);
        $copy->save();

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
            $copy->services()->save($serviceCopy);
        }
    }

    public function getError()
    {
        return $this->error;
    }
}
