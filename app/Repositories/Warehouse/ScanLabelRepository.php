<?php

namespace App\Repositories\Warehouse;

use App\Models\Order;
use App\Models\OrderTracking;

class ScanLabelRepository
{
    public $message;
    public $status;

    public function handle($order)
    {
        if($order->trackings->isNotEmpty() && $order->trackings()->latest()->first()->status_code >= Order::STATUS_DRIVER_RECIEVED){
            $this->message = 'package is included successfully.';
            $this->status = true;
            return;
        }

        if(!$order->is_paid){
            $this->message = 'package Payment is pending';
            $this->status = false;
            return;
        }

        if($order->status == Order::STATUS_CANCEL){
            $this->message = 'package is Canceled';
            $this->status = false;
            return;
        }
        
        if($order->status == Order::STATUS_REJECTED){
            $this->message = 'package is Rejected';
            $this->status = false;
            return;
        }
        
        if($order->status == Order::STATUS_RELEASE){
            $this->message = 'package is Release';
            $this->status = false;
            return;
        }

        if($order->status == Order::STATUS_REFUND){
            $this->message = 'package is Refunded';
            $this->status = false;
            return;
        }

        if($order->trackings->isNotEmpty() && $order->trackings()->latest()->first()->status_code >= Order::STATUS_PAYMENT_DONE && $order->trackings()->latest()->first()->status_code < Order::STATUS_ARRIVE_AT_WAREHOUSE){
            if (auth()->user()->isDriver()) {
                $this->addOrderTracking($order);
            }
            $this->message = 'package is included successfully';
            $this->status = true;
            return;
        }
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getStatus()
    {
        return $this->status;
    }

    private function addOrderTracking($order)
    {
        OrderTracking::create([
            'order_id' => $order->id,
            'status_code' => Order::STATUS_DRIVER_RECIEVED,
            'type' => 'HD',
            'description' => 'Driver picked up package',
            'country' => 'US',
            'city' => 'Miami'
        ]);

        return true;
    }
}
