<?php


namespace App\Repositories;


use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Services\Converters\UnitsConverter;
use App\Services\Correios\Models\PackageError;
use App\Services\Correios\Services\Brazil\Client;
use App\Services\Correios\Services\Brazil\CN23LabelMaker;

class CorrieosBrazilLabelRepository
{
    protected $error;

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
        $cn23 = $this->generateLabel($order);

        if ( $cn23 ){
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
        if (Auth::user()->can('stopPrintCorrieosLabel',Order::class) && !Auth::user()->isAdmin()){ 
          $this->error = 'Something went wrong in Correios API Please wait...';
          return null;
        }
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
