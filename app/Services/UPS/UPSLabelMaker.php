<?php

namespace App\Services\UPS;

use Illuminate\Support\Facades\Storage;
use File;

class UPSLabelMaker
{
    private $order;
    private $tracking_number;

    public function setOrder($order)
    {

        $this->order = $order;

        /**
         * Note...
         * us_api_tracking_code and us_api_response are the columns for 
         * second label(when there is a two label against order(UPS or USPS Label)
         */
        $this->tracking_number = ($order->has_second_label) ? $order->us_api_tracking_code : $order->corrios_tracking_code;
    }

    public function rotatePNGLabel()
    {
        $ups_response = ($this->order->has_second_label) ? json_decode($this->order->us_api_response)  : json_decode($this->order->api_response);

        $png_label = $ups_response->ShipmentResponse->ShipmentResults->PackageResults->ShippingLabel->GraphicImage;

        // storing label in as png
        Storage::put("labels/{$this->tracking_number}.png", base64_decode($png_label));

        // rotating the label image
        $png_label_path = Storage::path('labels/' . $this->tracking_number . '.png');

        $temp_image = imagecreatefrompng($png_label_path);
        $rotate = imagerotate($temp_image, -90, 0);
        imagepng($rotate, $this->tracking_number . '.png');

        //move rotated label image to storage
        $from_path = public_path($this->tracking_number . '.png');

        if (File::exists($from_path)) {
            File::move($from_path, $png_label_path);
        }

        return $png_label_path;
    }

    public function saveLabel()
    {
        $pdf = \PDF::loadView('labels.ups.index', ['corrios_tracking_code' => $this->tracking_number]);

        Storage::put("labels/{$this->tracking_number}.pdf", $pdf->output());

        return true;
    }

    public function deletePNGLabel()
    {
        File::delete(Storage::path('labels/' . $this->tracking_number . '.png'));

        return true;
    }
}
