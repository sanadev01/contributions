<?php


namespace App\Repositories;


use App\Models\Order;
use App\Models\OrderTracking;
use App\Services\Sinerlog\Client;
use Illuminate\Support\Facades\Log;
use App\Services\Converters\UnitsConverter;

class SinerlogLabelRepository
{
    protected $error;

    public function get(Order $order)
    {
        if ($order->getCN23() ){
            return $this->printLabel($order);
        }

        return $this->update($order);
    }

    public function update(Order $order)
    {
        $cn23 = $this->generateLabel($order);
        if ( $cn23->success == true ){

            /**
             * If label was successfully created, stores transction data on database
             */
            $arrCN23 = (array)$cn23;

            if($arrCN23['success']){
                $arrSinerlogReturn = (array)$arrCN23['data'];
                $arrSinerlogReturn = (array)$arrSinerlogReturn['data'];
                
                Log::info('Sinerlog Label Repository: update() - CN23 data: ');
                Log::info(json_encode($arrSinerlogReturn));
                
                $order->setCN23($arrSinerlogReturn);
                $order->setSinerlogTrxId($arrSinerlogReturn['order_code']);
                $order->setSinerlogFreight($arrSinerlogReturn['data']->freight_price);
            }

            $this->addOrderTracking($order);

            return $this->printLabel($order);
        }
        else {
            return $this->error = $cn23->message;
        }
    }

    public function printLabel(Order $order)
    {
        $labelPrinter = new Client();
        
        $data = $labelPrinter->getLabel($order);

        if($data->success){
            return $data->data;
        } else {
            return $this->error = 'An error occurred while generating the label';
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

    public function addOrderTracking($order)
    {
        if($order->trackings->isEmpty())
        {
            OrderTracking::create([
                'order_id' => $order->id,
                'status_code' => Order::STATUS_PAYMENT_DONE,
                'type' => 'HD',
                'description' => 'Order Placed',
                'country' => ($order->user->country != null) ? $order->user->country->code : 'US',
            ]);
        }    

        return true;
    }

}
