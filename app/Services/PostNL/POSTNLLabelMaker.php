<?php
namespace App\Services\POSTNL;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class POSTNLLabelMaker
{

    public function getContainerCN35($unit_response_list)
    {
        $response = json_decode($unit_response_list);
        $barcode = $response->barcode;
        $base64_manifest = $response->base64string;

        Storage::put("manifests/postnl/{$barcode}.pdf", base64_decode($base64_manifest));

        $path = storage_path().'/'.'app'.'/manifests/postnl/'.$barcode.''.'.pdf';

        if (file_exists($path)) {
            return Response::download($path);
        }

    }

}
