<?php
namespace App\Repositories;
use App\Models\Order;
use App\Models\OrderTracking;
use App\Facades\MileExpressFacade;
use App\Services\MileExpress\CN23LabelMaker;
use App\Services\Correios\Models\PackageError;

class MileExpressLabelRepository
{
    private $order;
    private $error;
    public $mileError;

    public function run(Order $order,$update)
    {
      $this->order = $order;
        if($update){
            return $this->updateLabel();
        }
        else {
            return $this->handle();
        }
    }

    public function handle()
    {
        if ($this->order->api_response == null) {
            $data = $this->getPrimaryLabel($this->order);
            if ( $data instanceof PackageError){
                $this->error = $data->getErrors();
                return null;
            }
    
            return $data;
        }
        $this->printCN23();
        return true;
    }

    public function updateLabel()
    {
        $code = optional(optional(optional($this->order)->recipient)->country)->code ?? 'BR';
        $this->order->update([
            'api_response' => null,
            'corrios_tracking_code' => 'HD'.date('d').date('m').substr(date('s'), 1, 1).$this->order->id.$code,
        ]); 
        $this->printCN23();        
        return true; 
    }

    public function getError()
    {
        return $this->error;
    }

    private function getPrimaryLabel($order)
    {
        $response = MileExpressFacade::createShipment($order);
        if ($response->success == true) {

            $order->update([
                'api_response' => json_encode($response->data),
                'corrios_tracking_code' => $response->data  ['trackingNumber'],
            ]);

            $order->refresh();
            $this->printCN23();

            // store order status in order tracking
            $this->addOrderTracking($order);

            return true;
        }
        return new PackageError("Error while creating parcel <br> Message:".$response->error['message']);

    }

    private function addOrderTracking()
    {
        if($this->order->trackings->isEmpty())
        {
            OrderTracking::create([
                'order_id' => $this->order->id,
                'status_code' => Order::STATUS_PAYMENT_DONE,
                'type' => 'HD',
                'description' => 'Order Placed',
                'country' => ($this->order->user->country != null) ? $this->order->user->country->code : 'US',
            ]);
        }

        return true;
    }

    private function printCN23()
    {
        $labelPrinter = new CN23LabelMaker();
        $labelPrinter->setOrder($this->order);
        $labelPrinter->setService($this->order->getService());
        $labelPrinter->setPacketType($this->order->getDistributionModality());
        $labelPrinter->saveAs(storage_path("app/labels/{$this->order->corrios_tracking_code}.pdf"));
    }
}
