<?php


namespace App\Repositories;


use App\Models\Order;
use App\Models\OrderTracking;
use App\Facades\CorreosChileFacade;
use App\Services\CorreosChile\CorreosChileLabelMaker;

class CorrieosChileLabelRepository
{
    protected $chile_errors;

    public function handle($order)
    {
        if(($order->shipping_service_name == 'SRP' || $order->shipping_service_name == 'SRM') && $order->api_response == null)
        {

            $this->generat_ChileLabel($order);

        }elseif($order->api_response != null)
        {

            $this->printLabel($order);
        }

    }

    public function update($order)
    {
        $this->generat_ChileLabel($order);
    }

    public function generat_ChileLabel($order)
    {
        if($order->shipping_service_name == 'SRP')
        {
            $this->getSRP($order);

        } else {
        
            $this->getSRM($order);
        }

        return $order->refresh();
    }

    public function getSRP($order)
    {
        $serviceType = 28;      //service code defined by correos chile

        $response = CorreosChileFacade::generateLabel($order, $serviceType);
        
        if($response->success == true)
        {
            //storing response in orders table
            $order->update([
                'api_response' => json_encode($response->data),
                'corrios_tracking_code' => $response->data->NumeroEnvio,
            ]);

            $this->addOrderTracking($order);
            
            $this->printLabel($order);
        } else {
            
            $this->chile_errors =  $response->message;
            return null;
        }
    }

    public function getSRM($order)
    {
        $serviceType = 32;      //service code defined by correos chile
        
        $response = CorreosChileFacade::generateLabel($order, $serviceType);

        if($response->success == true)
        {
            //storing response in orders table
            $order->update([
                'api_response' => json_encode($response->data),
                'corrios_tracking_code' => $response->data->NumeroEnvio,
            ]);

            $this->addOrderTracking($order);
            
            $this->printLabel($order);
        } else {
            
            $this->chile_errors =  $response->message;
            return null;
        }
    }

    public function getChileErrors()
    {   
        return $this->chile_errors;
    }

    public function printLabel(Order $order)
    {
        $labelPrinter = new CorreosChileLabelMaker();
        $labelPrinter->setOrder($order);
        $labelPrinter->saveLabel();

        return true;
    }

    public function addOrderTracking($order)
    {
        if($order->status == Order::STATUS_PAYMENT_DONE)
        {
            OrderTracking::create([
                'order_id' => $order->id,
                'status_code' => $order->status,
                'description' => 'Order Placed',
                'country' => $order->recipient->country->name,
            ]);
        }    

        return true;
    }
}