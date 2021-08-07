<?php


namespace App\Repositories;


use App\Models\Order;
use App\Facades\USPSFacade;
use App\Facades\CorreosChileFacade;
use App\Services\CorreosChile\CorreosChileLabelMaker;

class USPSLabelRepository
{
    protected $usps_errors;

    public function handle($order)
    {
        if(($order->shipping_service_name == 'Priority' || $order->shipping_service_name == 'FirstClass') && $order->chile_response == null)
        {

            $this->generat_USPSLabel($order);

        }elseif($order->chile_response != null)
        {

            // $this->printLabel($order);
        }
    }

    public function generat_USPSLabel($order)
    {
        if($order->shipping_service_name == 'Priority')
        {
            $this->getPriorityLabel($order);

        } else {
        
            $this->getFirstClass($order);
        }
    }

    public function getPriorityLabel($order)
    {
        $response = USPSFacade::generateLabel($order);
    }

    public function getFirstClass($order)
    {
        # code...
    }
     
    

    
}