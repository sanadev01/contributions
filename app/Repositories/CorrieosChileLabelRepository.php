<?php


namespace App\Repositories;


use App\Models\Order;
use App\Facades\CorreosChileFacade;
use App\Services\CorreosChile\CorreosChileLabelMaker;

class CorrieosChileLabelRepository
{
    protected $chile_errors;

    public function generat_ChileSRPLabel($order)
    {
        $serviceType = 28;      //service code defined by correos chile
        $order = Order::with('recipient', 'items')->find($order->id);
        $response = CorreosChileFacade::generateLabel($order, $serviceType);

        if($response->success == true)
        {
            //storing response in orders table
            $order->update([
                'chile_response' => json_encode($response->data),
                'corrios_tracking_code' => $response->data->NumeroEnvio,
            ]);
            
            $this->printLabel($order);
        } else {
            
            $this->chile_errors =  $response->message;
            return null;
        }
    }

    public function generat_ChileSRMLabel($order)
    {
        $serviceType = 32;      //service code defined by correos chile
        $order = Order::with('recipient', 'items')->find($order->id);
        $response = CorreosChileFacade::generateLabel($order, $serviceType);

        if($response->success == true)
        {
            //storing response in orders table
            $order->update([
                'chile_response' => json_encode($response->data),
                'corrios_tracking_code' => $response->data->NumeroEnvio,
            ]);
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
}