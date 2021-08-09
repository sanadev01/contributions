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
    
    

}
