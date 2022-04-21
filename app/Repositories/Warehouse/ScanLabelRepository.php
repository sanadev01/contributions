<?php

namespace App\Repositories\Warehouse;

use App\Models\Order;
use App\Models\OrderTracking;

class ScanLabelRepository
{
    public $message;
    public $statusCode;

    public function handle($order)
    {
        if($order->trackings->isNotEmpty() && $order->trackings()->latest()->first()->status_code >= Order::STATUS_DRIVER_RECIEVED){
            $this->message = 'package has already been scanned';
            $this->statusCode = 406;
            return;
        }

        if(!$order->is_paid){
            $this->message = 'package Payment is pending';
            $this->statusCode = 406;
            return;
        }

        if($order->status == Order::STATUS_CANCEL){
            $this->message = 'package is Canceled';
            $this->statusCode = 406;
            return;
        }
        
        if($order->status == Order::STATUS_REJECTED){
           $this->message = 'package is Rejected';
           $this->statusCode = 406;
           return;
        }
        
        if($order->status == Order::STATUS_RELEASE){
            $this->message = 'package is Release';
            $this->statusCode = 406;
            return;
        }

        if($order->status == Order::STATUS_REFUND){
            $this->message = 'package is Refunded';
            $this->statusCode = 406;
            return;
        }

        if($order->trackings->isNotEmpty() && $order->trackings()->latest()->first()->status_code >= Order::STATUS_PAYMENT_DONE && $order->trackings()->latest()->first()->status_code < Order::STATUS_ARRIVE_AT_WAREHOUSE){
            $this->addOrderTracking($order);
            $this->message = 'package is included successfully';
            $this->statusCode = 200;
            return;
        }
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    private function addOrderTracking($order)
    {
        OrderTracking::create([
            'order_id' => $order->id,
            'status_code' => Order::STATUS_DRIVER_RECIEVED,
            'type' => 'HD',
            'description' => 'Driver received from warehouse',
            'country' => 'US',
            'city' => 'Miami'
        ]);

        return true;
    }
}
