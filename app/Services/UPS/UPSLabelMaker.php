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

    public function rotatePNGLabel()
    {
        $ups_response = json_decode($this->order->api_response);
            
        $png_label = $ups_response->ShipmentResponse->ShipmentResults->PackageResults->ShippingLabel->GraphicImage;

        // storing label in as png
        Storage::put("labels/{$this->order->corrios_tracking_code}.png", base64_decode($png_label));

        // rotating the label image
        $png_label_path = Storage::path('labels/'.$this->order->corrios_tracking_code.'.png');
        
        $temp_image = imagecreatefrompng($png_label_path);
        $rotate = imagerotate($temp_image, -90, 0);
        imagepng($rotate, $this->order->corrios_tracking_code.'.png');

        //move rotated label image to storage
        $from_path = public_path($this->order->corrios_tracking_code.'.png');

        if(File::exists($from_path)){
            File::move($from_path, $png_label_path);
        }

        return $png_label_path;
    }

    public function saveLabel()
    {
        $pdf = \PDF::loadView('labels.ups.index', ['corrios_tracking_code' => $this->order->corrios_tracking_code]);
        
        Storage::put("labels/{$this->order->corrios_tracking_code}.pdf", $pdf->output());
        
        return true;
    }

    public function deletePNGLabel()
    {
        File::delete(Storage::path('labels/'.$this->order->corrios_tracking_code.'.png'));

        return true;
    }

}
