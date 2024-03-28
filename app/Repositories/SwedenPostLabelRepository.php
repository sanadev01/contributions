<?php


namespace App\Repositories;

use Carbon\Carbon;
use App\Models\Order;
use App\Services\SwedenPost\Client;
use Illuminate\Support\Facades\Storage;
use App\Services\Correios\Models\PackageError;
use App\Services\SwedenPost\Services\UpdateCN23Label;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;

class SwedenPostLabelRepository
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
            if (optional($order->order_date)->greaterThanOrEqualTo(Carbon::parse('2024-02-21'))) {
                $base64Pdf = $response[1]->data[0]->labelContent;
            } else {
                $base64Pdf = $response->data[0]->labelContent;
            }

            // $invoicePdf = optional($response[1]->data[0])->invoiceContents[0];

            // if ($invoicePdf) {
            //     $pdf = PDFMerger::init();
            //     $labelContent = base64_decode($base64Pdf);
            //     $pdf->addString($labelContent);
            //     $pdf->addString(base64_decode($invoicePdf));
            //     $pdf->merge();

            //     $mergedPdfPath = app()->basePath('storage').("/app/labels/{$order->corrios_tracking_code}.pdf");

            //     $mergedPdf = $pdf->output();
            //     file_put_contents($mergedPdfPath, $mergedPdf);
            // } else {
                Storage::put("labels/{$order->corrios_tracking_code}.pdf", base64_decode($base64Pdf));
            // }
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
