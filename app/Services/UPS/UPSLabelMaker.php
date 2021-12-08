<?php
namespace App\Services\UPS;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use File;

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
            $ups_response = json_decode($this->order->api_response);
            
            $png_label = $ups_response->ShipmentResponse->ShipmentResults->PackageResults->ShippingLabel->GraphicImage;
            Storage::put("temp/labels/{$this->order->corrios_tracking_code}.png", base64_decode($png_label));

            $temp_label_path = Storage::path('temp/labels/'.$this->order->corrios_tracking_code.'.png');
            $this->rotateLabel($temp_label_path);
            dd(true);
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

    private function rotateLabel($path)
    {
        $image = imagecreatefrompng($path);
        $rotate = imagerotate($image, -90, 0);
        imagepng($rotate, 'rotated_label.png');

        return true;

    }

}
