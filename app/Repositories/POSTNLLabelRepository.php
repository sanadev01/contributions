<?php


namespace App\Repositories;

use App\Models\Order;
use App\Services\Converters\UnitsConverter;
use Illuminate\Support\Facades\Storage;
use App\Services\Correios\Models\PackageError;
use App\Services\PostNL\Client;
use App\Services\PostNL\CN23LabelMaker;


class POSTNLLabelRepository
{
    protected $error;

    public function run(Order $order,$update)
    {
        // if($update){
        //     return $this->update($order);
        // }
        // else {
            return $this->get($order);
        // }
    }
    public function get(Order $order)
    {
        if ( $order->getCN23() ){
            return true;
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
            $postnl_response = json_decode($order->api_response);
            $base64_pdf = $postnl_response->data->base64string;
            Storage::put("labels/{$order->corrios_tracking_code}.pdf", base64_decode($base64_pdf));

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
