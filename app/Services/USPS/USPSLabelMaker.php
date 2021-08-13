<?php
namespace App\Services\USPS;

use App\Models\Order;
use Barryvdh\DomPDF\PDF;
use Illuminate\Support\Facades\Storage;


class USPSLabelMaker
{
    private $order;

    public function setOrder($order)
    {
        
        $this->order = $order;
    }

    public function saveLabel()
    {
        $api_response = json_decode($this->order->api_response);
        $base64_pdf = $api_response->base64_labels[0];

        Storage::put("labels/{$this->order->corrios_tracking_code}.pdf", base64_decode($base64_pdf));
    }
    
    public function getContainerCN35($unit_response_list)
    {
        $response = json_decode($unit_response_list);
        $manifest_number = $response->usps[0]->manifest_number;
        $base64_manifest = $response->usps[0]->base64_manifest;
        
        Storage::put("manifests/usps/{$manifest_number}.pdf", base64_decode($base64_manifest)); 
    }
    

}
