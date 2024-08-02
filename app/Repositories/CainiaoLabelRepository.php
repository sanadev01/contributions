<?php


namespace App\Repositories;

use App\Models\Order;
use App\Services\Cainiao\Client;
use App\Services\Cainiao\CN23LabelMaker;

class CainiaoLabelRepository
{
    protected $error;
    protected $order;

    public function run(Order $order)
    {
        $this->order = $order;
        if($order->cn23){
            return $this->printLabel($order);
        }

        if($this->generateLabel($order))
        {
            $this->printLabel($order);
        }
        return null;
    }
     
    public function printLabel(Order $order)
    {
        $labelPrinter = new CN23LabelMaker();
        $labelPrinter->setOrder($order);
        $labelPrinter->setService($order->getService());
        $labelPrinter->saveAs(storage_path("app/labels/{$order->corrios_tracking_code}.pdf"));
    }
    protected function generateLabel(Order $order)
    {
        $client = new Client();
        $client->createPackage($order); 
        $this->error =  $client->error;
        if($this->error){
            return false;
        }
        return true;
    }
     
    public function getError()
    {
        return $this->error;
    }

}