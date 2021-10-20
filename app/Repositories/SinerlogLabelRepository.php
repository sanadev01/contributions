<?php


namespace App\Repositories;


use App\Models\Order;
use App\Services\Converters\UnitsConverter;
use App\Services\Sinerlog\Client;


class SinerlogLabelRepository
{
    protected $error;

    public function get(Order $order)
    {
        if ( $order->getCN23() ){
            return $this->printLabel($order);
        }

        return $this->update($order);
    }

    public function update(Order $order)
    {
        $cn23 = $this->generateLabel($order);

        if ( $cn23 ){

            /**
             * If label was successfully created, stores transction data on database
             */
            $arrCN23 = (array)$cn23;

            if($arrCN23['success']){
                $arrSinerlogReturn = (array)$arrCN23['data'];
                $arrSinerlogReturn = (array)$arrSinerlogReturn['data'];
                
                $order->setCN23($arrSinerlogReturn);
                $order->setSinerlogTrxId($arrSinerlogReturn['order_code']);
                $order->setSinerlogFreight($arrSinerlogReturn['data']->freight_price);
            }

            return $this->printLabel($order);
        }
        else {
            return 'An error occurred while generating the label';
        }
    }

    public function printLabel(Order $order)
    {
        $labelPrinter = new Client();
        
        $data = $labelPrinter->getLabel($order);

        if($data->success){
            return $data->data;
        } else {
            return 'An error occurred while generating the label';
        }
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
