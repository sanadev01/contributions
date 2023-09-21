<?php
namespace App\Services\USPS;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;


class USPSLabelMaker
{
    public function saveLabel($base64_pdf, $fileName)
    {
        Storage::put("labels/{$fileName}.pdf", base64_decode($base64_pdf));
    }
    
    public function getContainerCN35($unit_response_list)
    {
        $response = json_decode($unit_response_list);
        $manifest_number = $response->usps[0]->manifest_number;
        $base64_manifest = $response->usps[0]->base64_manifest;
        
        Storage::put("manifests/usps/{$manifest_number}.pdf", base64_decode($base64_manifest));

        $path = storage_path().'/'.'app'.'/manifests/usps/'.$manifest_number.''.'.pdf';
        
        if (file_exists($path)) {
            return Response::download($path);
        }
        
    }

}
