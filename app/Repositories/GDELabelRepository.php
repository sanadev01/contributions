<?php


namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderTracking;
use App\Models\ShippingService;
use App\Services\GDE\GDEService;
use App\Services\GDE\GDELabelMaker;

class GDELabelRepository
{    
    protected $error;

    public function run(Order $order, $update)
    {
        return $this->get($order);
    }

    public function get(Order $order)
    {
        if ($order->isPaid() && !$order->api_response){
            return $this->printLabel($order);
        }
        return $this->update($order);
    }

    public function update(Order $order)
    {
        $cn23 = $this->generateLabel($order);
        if ( $cn23 ){
            $this->printLabel($order);
        }
        return null;
    }

    private function printLabel(Order $order)
    {        
        if(!$order->api_response)
        {
            return $this->generateLabel($order);
        }else {
            $gdeService = new GDEService();
            return $gdeService->printLabel($order);
        }
    }

    protected function generateLabel(Order $order)
    {
        $gdeService = new GDEService();
        return $gdeService->generateLabel($order);
    }

    public function getError()
    {
        return $this->error;
    }
}