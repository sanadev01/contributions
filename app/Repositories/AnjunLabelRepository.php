<?php


namespace App\Repositories;


use App\Models\Order;
use App\Services\Anjun\AnjunClient;
use App\Services\Correios\Services\Brazil\CN23LabelMaker;
use App\Traits\PrintOrderLabel;
use Illuminate\Http\Request;


class AnjunLabelRepository
{
use PrintOrderLabel;

    public $order;
    public $error;
    public $request;
    public function __construct(Request $request,Order $order)
    {
        $this->order = $order;
        $this->error = null;
        $this->request = $request;
    }

    public function execute()
    {
        $response = $this->get($this->order);  
        $this->order->refresh(); 
        $data = $response->getData();
        if(!$data->success){ 
             $this->error = $data->message; 
        }
        return $this->renderLabel($this->request, $this->order, $this->error); 
    }

    public function get(Order $order)
    {
        if ($order->getCN23()) {
            return $this->printLabel($order);
        } else {
            return $this->update($order);
        }
    }

    public function update(Order $order)
    {
        $anjunClient = new AnjunClient();
        $response = $anjunClient->createPackage($order);
        $data = $response->getData();
        if ($data->success) {
            return $this->printLabel($order);
        } else {
            return $response;
        }
    }

    public function printLabel(Order $order)
    {
        $labelPrinter = new CN23LabelMaker();
        $labelPrinter->setOrder($order);
        $labelPrinter->setService($order->getService());
        $labelPrinter->setPacketType($order->getDistributionModality());
        $labelPrinter->saveAs(storage_path("app/labels/{$order->corrios_tracking_code}.pdf"));
        return responseSuccessful(null, 'Label Printer Success');
    }
}
