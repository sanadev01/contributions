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
    public function __construct(Order $order, Request $request)
    {
        $this->order = $order;
        $this->error = null;
        $this->request = $request;
    }
    public function run()
    {
        return $this->get($this->order);
    }
    public function get(Order $order)
    {
        if ($order->getCN23()) {
        $this->printLabel($order);
        return null;
        }
          return $this->update($order);
    }
    public function update(Order $order)
    {
        $cn23 = $this->generateLabel($order);
        if ($cn23) {
            $this->printLabel($order);
        }
        return null;
    }
    protected function generateLabel(Order $order)
    {
        $anjunClient = new AnjunClient();
        $response = $anjunClient->createPackage($order);
        $data = $response->getData();
        if ($data->success) {
            return $this->printLabel($order);
        } else { 
            $this->error= $data->message;
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
    public function getError()
    {
        return $this->error;
    }
}
