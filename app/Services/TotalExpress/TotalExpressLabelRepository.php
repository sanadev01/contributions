<?php


namespace App\Services\TotalExpress;
use App\Models\Order;
use App\Services\TotalExpress\Client;
use Illuminate\Support\Facades\Storage; 
use App\Services\Correios\Models\PackageError;
use App\Services\TotalExpress\Services\UpdateCN23Label;



class TotalExpressLabelRepository
{
    protected $error;

    public function run(Order $order, $update)
    {
        if($update){
            return $this->update($order);
        }
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
        $cn23 = $this->generateLabel($order);
        if ( $cn23 ){
            $this->printLabel($order);
        }
        return null;
    }

    private function printLabel(Order $order)
    {
        if($order->api_response)
        { 
            $client = new Client();
            $data = $client->labelUrlUpdate($order);
            if ( $data instanceof PackageError){
                $this->error = $data->getErrors();
                return false;
            }
                return true;
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