<?php


namespace App\Repositories;

use App\Models\Order;
use Illuminate\Support\Facades\Storage;
use App\Services\Correios\Models\PackageError;
use App\Services\HoundExpress\Client;
class HoundExpressLabelRepository
{
    protected $error;

    public function run(Order $order,$update)
    {
        return $this->get($order);
    }

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
        if ($cn23){
            $this->printLabel($order);
        }
        return null;
    }

    private function printLabel(Order $order)
    {
        if($order->api_response)
        {
            $response = json_decode($order->api_response);
            $base64Pdf = $response->data[0]->labelContent;
            Storage::put("labels/{$order->corrios_tracking_code}.pdf", base64_decode($base64Pdf));
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

    function generateMasterAirWayBill($deliveryBill) { 
        
        $client = new Client();
        return  $client->generateMasterAirWayBill($deliveryBill); 
    }
    public function getError()
    {
        return $this->error;
    }

}
