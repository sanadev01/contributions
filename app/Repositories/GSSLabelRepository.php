<?php


namespace App\Repositories;

use App\Models\Order;
use App\Services\GSS\Client;
use Illuminate\Support\Facades\Storage;
use App\Services\Converters\UnitsConverter;
use App\Services\Correios\Models\PackageError;
use App\Services\GSS\Services\UpdateCN23Label;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;


class GSSLabelRepository
{
    protected $error;

    public function run(Order $order, $update)
    {
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
            if(count($response->labels) > 1) {
                $this->addLabelPages($response);
            } else {
                Storage::put("labels/{$order->corrios_tracking_code}.pdf", base64_decode($response->labels[0]));
            }
            return true;
            // return (new UpdateCN23Label($order))->run(); 
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

    private function addLabelPages($response) {

        $pdf = PDFMerger::init();
        $label = "app/labels/{$response->trackingNumber}";
        $page1 = storage_path("$label(1).pdf");
        $page2 = storage_path("$label(2).pdf");
        file_put_contents($page1, base64_decode($response->labels[0]));
        file_put_contents($page2, base64_decode($response->labels[1]));
        $pdf->addPDF($page1);
        $pdf->addPDF($page2);
        $pdf->merge();
        unlink(storage_path("$label(1).pdf"));
        unlink(storage_path("$label(2).pdf"));
        
        $pdf->save(storage_path("$label.pdf"));
    }

}