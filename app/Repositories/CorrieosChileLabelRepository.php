<?php


namespace App\Repositories;


use App\Models\Order;
use App\Models\OrderTracking;
use App\Models\ShippingService;
use App\Facades\CorreosChileFacade;
use App\Services\CorreosChile\CorreosChileLabelMaker;
use App\Services\CourierExpress\CourrierExpressService;

class CorrieosChileLabelRepository
{
    protected $chile_errors;
    
    public function run(Order $order,$update)
    {
        if($update){
            return $this->update($order);
        }
        else {
            return $this->handle($order);
        }
    }

    public function handle($order)
    {
        if($order->isPaid() && !$order->api_response)
        {
            if($order->shippingService->service_sub_class == ShippingService::SRP || $order->shippingService->service_sub_class == ShippingService::SRM)
            {
               return $this->generatChileLabel($order);
            }

            if($order->shippingService->service_sub_class == ShippingService::Courier_Express)
            {
                return $this->generateCourierExpressLabel($order);
            }
            
        }

        if($order->shippingService->service_sub_class == ShippingService::Courier_Express)
        {
            return $this->generateCourierExpressLabel($order);
        }

        return $this->printLabel($order);

    }

    public function update($order)
    {
        if($order->shippingService->service_sub_class == ShippingService::Courier_Express)
        {
            return $this->generateCourierExpressLabel($order);
        }
        
        $this->generatChileLabel($order);
    }

    public function generatChileLabel($order)
    {
        if($order->shippingService->service_sub_class == ShippingService::SRP)
        {
            $this->getSRP($order);

        } else {
        
            $this->getSRM($order);
        }

        return $order->refresh();
    }

    public function getSRP($order)
    {
        $serviceType = ShippingService::SRP;      //service code defined by correos chile

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
        $serviceType = ShippingService::SRM;      //service code defined by correos chile
        
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
        if($order->trackings->isEmpty())
        {
            OrderTracking::create([
                'order_id' => $order->id,
                'status_code' => Order::STATUS_PAYMENT_DONE,
                'type' => 'HD',
                'description' => 'Order Placed',
                'country' => ($order->user->country != null) ? $order->user->country->code : 'US',
            ]);
        }    

        return true;
    }

    public function generateCourierExpressLabel($order)
    {
        $courierExpressService = new CourrierExpressService();
        return $courierExpressService->generateLabel($order);
    }
}