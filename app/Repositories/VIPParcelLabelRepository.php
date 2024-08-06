<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\VipParcel\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Services\Correios\Models\PackageError;



class VIPParcelLabelRepository
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
            $response = json_decode($order->api_response);
            $pdfUrl = $response->images[0];
            $fileResponse = Http::get($pdfUrl);

            if ($fileResponse->successful()) {
                $pdfContents = $fileResponse->body();
                $storagePath = "labels/{$order->corrios_tracking_code}.pdf";
                Storage::put($storagePath, $pdfContents);

                return true;
            }

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