<?php
namespace App\Services\USPS;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;


class UPSLabelMaker
{
    private $order;

    public function setOrder($order)
    {
        
        $this->order = $order;
    }

    public function saveLabel()
    {
        if($this->order->api_response != null)
        {
            $usps_response = json_decode($this->order->api_response);
            $base64_pdf = $usps_response->base64_labels[0];

            Storage::put("labels/{$this->order->corrios_tracking_code}.pdf", base64_decode($base64_pdf));

            return true;
        }
    }
    
    public function saveUSPSLabel()
    {
        if($this->order->usps_response != null)
        {
            $usps_response = json_decode($this->order->usps_response);
            $base64_pdf = $usps_response->base64_labels[0];

            Storage::put("labels/{$this->order->corrios_usps_tracking_code}.pdf", base64_decode($base64_pdf));

            return true;
        }
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
