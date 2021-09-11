<?php


namespace App\Repositories;


use App\Models\Order;
use App\Facades\USPSFacade;
use App\Models\OrderTracking;
use App\Services\USPS\USPSLabelMaker;


class USPSLabelRepository
{
    protected $usps_errors;

    public function handle($order)
    {
        if(($order->shipping_service_name == 'Priority' || $order->shipping_service_name == 'FirstClass') && $order->api_response == null)
        {
    
            $this->generat_USPSLabel($order);

        }elseif($order->api_response != null)
        {
            
            $this->printLabel($order);
        }
    }

    public function update($order)
    {
        $this->generat_USPSLabel($order);
    }

    public function generat_USPSLabel($order)
    {
        $response = USPSFacade::generateLabel($order);

        if($response->success == true)
        {
            //storing response in orders table
            $order->update([
                'api_response' => json_encode($response->data),
                'corrios_tracking_code' => $response->data['usps']['tracking_numbers'][0],
            ]);
            // store order status in order tracking
            $this->addOrderTracking($order);

            $this->printLabel($order);

        } else {

            $this->usps_errors = $response->message;
            return null;
        }
        
    }

    public function printLabel(Order $order)
    {
        $labelPrinter = new USPSLabelMaker();
        $labelPrinter->setOrder($order);
        $labelPrinter->saveLabel();

        return true;
    }

    public function getUSPSErrors()
    {
        return $this->usps_errors;
    }

    public function addOrderTracking($order)
    {
        if($order->trackings->isEmpty())
        {
            OrderTracking::create([
                'order_id' => $order->id,
                'status_code' => $order->status,
                'type' => 'HD',
                'description' => 'Order Placed',
                'country' => ($order->user->country != null) ? $order->user->country->code : 'US',
            ]);
        }    

        return true;
    }
 

    
}