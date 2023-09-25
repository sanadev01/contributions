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
            $label = $this->makePDFLabel($response);

            Storage::put("labels/{$order->corrios_tracking_code}.pdf", base64_decode($label));
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

    private function makePDFLabel($response) {
        $pdf = PDFMerger::init();
        $label = "app/labels/{$response->trackingNumber}";
        foreach ($response->labels as $index => $labelBase64) {
            $labelContent = base64_decode($labelBase64);
            $pagePath = storage_path("{$label}_{$index}.pdf");
            file_put_contents($pagePath, $labelContent);
            $pdf->addPDF($pagePath);
        }
        $pdf->merge();
        
        // Remove individual pages
        foreach ($response->labels as $index => $labelBase64) {
            $pagePath = storage_path("{$label}_{$index}.pdf");
            unlink($pagePath);
        }
        $mergedPdf = $pdf->output();
        return base64_encode($mergedPdf);
    }

}