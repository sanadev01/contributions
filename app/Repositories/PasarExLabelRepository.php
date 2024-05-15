<?php


namespace App\Repositories;

use App\Models\Order;
use App\Services\PasarEx\CN23LabelMaker;

class PasarExLabelRepository
{
    protected $error;
    protected $order;

    public function run(Order $order, $update)
    {
        $this->order = $order;
        return $this->get($order);
    }

    public function get(Order $order)
    {
        if ( $order->getCN23() ){
            return $this->printLabel($order);
        }
        return $this->update($order);
    }

    public function update(Order $order)
    {
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

        $order->update([
            'corrios_tracking_code' => $this->getTrackingCode(),
            'cn23' => [
                "tracking_code" => $this->getTrackingCode(),
                "stamp_url" => route('warehouse.cn23.download', $order->id),
                'leve' => false
            ],
        ]);
        return true;
    }
    public function getTrackingCode()
    {
        $tempWhr =  $this->order->change_id;        
        switch(strlen($tempWhr)){
            case(5):
                $tempWhr = (str_pad($tempWhr, 10, '32023', STR_PAD_LEFT));
                break;
                case(6):
                    $tempWhr = (str_pad($tempWhr, 10, '2023', STR_PAD_LEFT));
                    break;
                    case(7):
                        $tempWhr = (str_pad($tempWhr, 10, '023', STR_PAD_LEFT));
                        break;
                        case(8):
                                $tempWhr = (str_pad($tempWhr, 10, '23', STR_PAD_LEFT)); 
                            break;
                            case(9):
                                $tempWhr = (str_pad($tempWhr, 10, '3', STR_PAD_LEFT));
                                break;
        }
        return 'HD'."{$tempWhr}"."CO";
    }

    public function getError()
    {
        return $this->error;
    }

}