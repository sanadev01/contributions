<?php


namespace App\Repositories;


use App\Models\Order;
use App\Services\Anjun\AnjunClient;
use App\Services\Correios\Services\Brazil\CN23LabelMaker;
class AnjunLabelRepository
{

    public $error;
    public function __construct()
    {
        $this->error = null;
    }

    public function run(Order $order,$update)
    {
        if($update){
            return $this->update($order);
        }
        else {
            return $this->get($order);
        }
    }

    public function get(Order $order)
    {
        if ( $order->getCN23() ){
            $this->printLabel($order);
            return null;
        }
        return $this->update($order);
        
    }

    public function update(Order $order)
    {
        $cn3 = $this->generateLabel($order);
        if ( $cn3 ){
            $this->printLabel($order);
        }
        return null;
    }

    public function printLabel(Order $order)
    { 
        $labelPrinter = new CN23LabelMaker();
        $labelPrinter->setOrder($order);
        $labelPrinter->setService($order->getService());
        $labelPrinter->setPacketType($order->getDistributionModality());
        $labelPrinter->saveAs(storage_path("app/labels/{$order->corrios_tracking_code}.pdf"));
        
    }

    protected function generateLabel(Order $order)
    {
        $anjunClient = new AnjunClient();
        $response = $anjunClient->createPackage($order);
        $data = $response->getData();
        if(!$data->success){ 
            $this->error = $data->message; 
            return null;
       }
       
        if ($data->success) {
             $this->printLabel($order);
             return null;
        }

        return $data;
    }

    public function getError()
    {
        return $this->error;
    }
}
