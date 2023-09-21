<?php

namespace App\Services\FedEx;

use App\Services\ZPLConverter;
use Illuminate\Support\Facades\Storage;

class FedExLabelMaker
{
    private $apiResponse;
    private $trackingCode;
    private $pdf;

    public function __construct($apiResponse, $trackingCode)
    {
        $this->apiResponse = json_decode($apiResponse);
        $this->trackingCode = $trackingCode;
    }

    public function convertLabelToPdf()
    {
        $base64Label = $this->apiResponse->output->transactionShipments[0]->pieceResponses[0]->packageDocuments[0]->encodedLabel;
        $label = base64_decode($base64Label);

        $zplConverter = new ZPLConverter();
        $response = $zplConverter->convertToPdf($label);

        if($response['success'] == true)
        {
            $this->pdf = $response['label'];

            return true;
        }

        return false;
    }

    public function saveLabel()
    {
        Storage::put("labels/{$this->trackingCode}.pdf", $this->pdf);

        return true;
    }
}
