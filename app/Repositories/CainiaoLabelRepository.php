<?php


namespace App\Repositories;

use App\Models\Order;
use App\Services\Cainiao\Client;
use App\Services\Cainiao\CN23LabelMaker;

class CainiaoLabelRepository
{
    protected $error;
    protected $order;

    public function run(Order $order, $update)
    {
        $this->order = $order;
        if ($update) { 
            \Log::info('updating the cainiao order label');
            if ($this->updateLabel($order)) {
                \Log::info('updated the cainiao order label');
                return $this->printLabel($order);
            }
        }
        if ($order->cn23) {
            return $this->printLabel($order);
        }
        if ($this->generateLabel($order)) {
            return $this->printLabel($order);
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
        if ($this->error) {
            return false;
        }
        return true;
    }


    protected function updateLabel(Order $order)
    {
        $client = new Client();
        $client->updatePackage($order);
        $this->error =  $client->error;
        if ($this->error) {
            return false;
        }
        return true;
    }

    public function getError()
    {
        return $this->error;
    }
}
