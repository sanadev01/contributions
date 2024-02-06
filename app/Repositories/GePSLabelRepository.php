<?php


namespace App\Repositories;

use App\Models\Order;
use App\Models\ShippingService;
use App\Services\Converters\UnitsConverter;
use Illuminate\Support\Facades\Storage;
use App\Services\Correios\Models\PackageError;
use App\Services\GePS\Client;
use App\Services\GePS\Services\UpdateCN23Label;
use Illuminate\Support\Facades\Auth;

class GePSLabelRepository
{
    protected $error;

    public function run(Order $order,$update)
    {
            return $this->get($order);
    }

    public function get(Order $order)
    {
        if ( $order->getCN23() ){
            $this->printLabel($order);
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
            $geps_response = json_decode($order->api_response);
            $base64_pdf = $geps_response->shipmentresponse->label;
            Storage::put("labels/{$order->corrios_tracking_code}.pdf", base64_decode($base64_pdf)); 
            if($order->shippingService->service_sub_class == ShippingService::GePS || $order->shippingService->service_sub_class == ShippingService::Parcel_Post) {
                return (new UpdateCN23Label($order))->run(); 
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
