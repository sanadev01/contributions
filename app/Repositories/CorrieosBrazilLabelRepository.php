<?php


namespace App\Repositories;


use App\Models\Order;
use App\Services\Converters\UnitsConverter;
use App\Services\Correios\Models\PackageError;
use App\Services\Correios\Services\Brazil\Client;
use App\Services\Correios\Services\Brazil\CN23LabelMaker;

class CorrieosBrazilLabelRepository
{
    protected $error;

    public function get(Order $order)
    {
        if ( $order->getCN23() ){
            $labelPrinter = new CN23LabelMaker();

            $labelPrinter->setOrder($order);
            $labelPrinter->setService(2);
            $labelPrinter->saveAs(storage_path("app/labels/{$order->corrios_tracking_code}.pdf"));
        }

        return $this->update($order);
    }

    public function update(Order $order)
    {
        $cn23 = $this->generateLabel($order);

        if ( $cn23 ){
            $order->setCN23( (array) $cn23);
            $labelPrinter = new CN23LabelMaker();

            $labelPrinter->setOrder($order);
            $labelPrinter->setService(2);
            $labelPrinter->saveAs(storage_path("app/labels/{$order->corrios_tracking_code}.pdf"));
        }

        return null;
    }

    protected function generateLabel(Order $order)
    {
        $client = new Client();
        $data = $client->createPackage($order);
        if ( $data instanceof PackageError){
            $this->error = $data->getErrors();
            return null;
        }

        return $data;
    }

    public function getError()
    {
        return $this->error;
    }
}
