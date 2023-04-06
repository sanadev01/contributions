<?php

namespace App\Services\PostPlus\Services;

use App\Models\Order;
use App\Models\ShippingService;
use App\Services\Common\PDFI\PDFRotate;

class UpdateCN23Label
{
    private $order;
    private $pdf_file;
    private $pdfi;
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->pdf_file = storage_path("app/labels/{$this->order->corrios_tracking_code}.pdf");
        $this->pdfi = new PDFRotate('L', 'mm', array(152, 104));
    }
    public function run()
    {
        if($this->order->shippingService->service_sub_class == ShippingService::Post_Plus_Registered || $this->order->shippingService->service_sub_class == ShippingService::Post_Plus_Prime) {
            // initiate FPDI
            $this->pdfi = new PDFRotate('L', 'mm', array(152, 104));
            // $this->pdfi->setPrintHeader(false);
            $this->pdfi->AddPage();
            $this->pdfi->SetMargins(0, 0, 0);
            // set the source file 
            $this->pdfi->setSourceFile($this->pdf_file);
            // import page 1
            $tplId = $this->pdfi->importPage(1);
            // use the imported page and place it at point 0,0
            $this->pdfi->useTemplate($tplId, 0, 0);
            $this->pdfi->SetFillColor(255, 255, 255);
            $this->pdfi->SetFont("Arial", "", 7);
            //FOR SENDER NAME & ADDRESS
            // $this->pdfi->SetFont("Arial", "B", 5);
            // $this->pdfi->RotatedText(60.7, 20, 'Sender:', 0);
            // $this->pdfi->SetFont("Arial", "B", 5);
            // $this->pdfi->RotatedText(60.7, 22, $this->order->getSenderFullName(), 0);
            // $this->pdfi->SetFont("Arial", "", 5);
            // $this->pdfi->RotatedText(60.7, 24, "2200 NW 129TH AVE", 0);
            // $this->pdfi->SetFont("Arial", "", 5);
            // $this->pdfi->RotatedText(60.7, 26, "United States 33182 Miami FL", 0);
            //FOR SHIPPING
            $this->pdfi->SetFont("Arial", "B", 5);
            $this->pdfi->RotatedText(4, 47.5, 'Shipping:', 00);
            //FOR SHIPPING COST
            $this->pdfi->SetFont("Arial", "B", 5);
            $userDeclaredFreight = $this->order->user_declared_freight <= 0.01 ? 0 : $this->order->user_declared_freight;
            $this->pdfi->RotatedText(44, 47.5, number_format($userDeclaredFreight, 2, '.', ','), 0);
            //FOR TOTAL ORDER VALUE
            $this->pdfi->SetFillColor(255, 255, 255);
            $this->pdfi->Rect(65, 99, 7, 9, "F");
            $this->pdfi->SetFont("Arial", "B", 5);
            $this->pdfi->RotatedText(44, 64, number_format($userDeclaredFreight + $this->order->order_value, 2, '.', ','), 0);
            //FOR CPF#
            // if ($this->order->recipient->tax_id) {
            //     $this->pdfi->SetFont("Arial", "B", 8);
            //     $this->pdfi->RotatedText(66, 51, 'CPF: ' . $this->order->recipient->tax_id, 0);
            // }
            //FOR REFERENCE NO
            // if ($this->order->customer_reference || $order->tracking_id) {
            //     $this->pdfi->SetFont("Arial", "B", 8);
            //     $this->pdfi->RotatedText(96, 51, 'Ref#: ' . ($this->order->customer_reference ? $this->order->customer_reference : $this->order->tracking_id).' HD-'.$this->order->id, 0);
            // }    
            $this->pdfi->Output($this->pdf_file, 'F');
            return true;

        } elseif ($this->order->shippingService->service_sub_class == ShippingService::Post_Plus_EMS) {
            // initiate FPDI
            if(count($this->order->items) > 5) {
                $this->pdfi = new PDFRotate('P', 'mm', array(212.9, 275));
                $this->textEMSLabel(118.7, 212.5, 135.8, 212.5, 136, 215, 12.5, 234.3, 133.1, 213, 118.6, 211.1, 118.7, 215);
                
            }else {
                $this->pdfi = new PDFRotate('L', 'mm', array(147, 212.9));
                $this->textEMSLabel(118.7, 92.5, 135.8, 92.5, 136, 95.2, 12.5, 114.7, 133.8, 92.9, 117.8, 91.2, 118.7, 95.3);
            }
            $this->pdfi->Output($this->pdf_file, 'F');
            return true;

        } elseif($this->order->shippingService->service_sub_class == ShippingService::Post_Plus_Premium) {
            $this->pdfi = new PDFRotate('L', 'mm', array(147, 212.9));
            $this->textEMSLabel(116.7, 92.5, 135.8, 92.5, 135, 95.2, 12.5, 114.7, 131.8, 92.9, 116.8, 91.2, 116.7, 95.2);
            //FOR CPF
            $this->pdfi->SetFont("Arial", "B", 7);
            $this->pdfi->RotatedText(186, 29.2, 'CPF:', 0);
            //FOR items
            foreach($this->order->items  as $key=>$item){
                if($key>4){
                    break;
                }
                $this->pdfi->SetFont("Arial", "B", 7);
                $this->pdfi->RotatedText(142, 74+($key*4), $item->sh_code, 0);
                //FOR items
                $this->pdfi->SetFont("Arial", "B", 7);
                $this->pdfi->RotatedText(175.5, 74+($key*4), 'USA', 0);
            }
            // FOR RETURN ADDRESS
            $this->pdfi->SetFillColor(255, 255, 255);
            $this->pdfi->Rect(60, 22, 40, 12, "F");
            $this->pdfi->SetFont("Arial", "", 6);
            $this->pdfi->RotatedText(60.7, 26, "(em caso de nao entrega encaminhar para)", 0);
            $this->pdfi->SetFont("Arial", "", 6);
            $this->pdfi->RotatedText(60.7, 29, "Blue Line Ag. De Cargas Ltda.", 0);
            $this->pdfi->SetFont("Arial", "", 6);
            $this->pdfi->RotatedText(60.7, 32, "Rua Barao Do Triunfo, 520-CJ 152- Brooklin", 0);
            $this->pdfi->SetFont("Arial", "", 6);
            $this->pdfi->RotatedText(60.7, 35, "Paulista CEP 04602-001 - Sao Paulo - SP- Brasil", 0);
            //FOR BOX CHECK
            $this->pdfi->SetFont("Arial", "B", 7);
            $this->pdfi->RotatedText(11.7, 102.7, 'X', 0);
            $this->pdfi->Output($this->pdf_file, 'F');
            
            return true;
        }
    }
    
    public function textEMSLabel($sw, $sh, $cw, $ch, $fw, $fh, $rw, $rh, $bw, $bh, $sbw, $sbh, $tvw, $tvh) {

        // $this->pdfi->setPrintHeader(false);
        $this->pdfi->AddPage();
        $this->pdfi->SetMargins(0, 0, 0);
        // set the source file 
        $this->pdfi->setSourceFile($this->pdf_file);
        // import page 1
        $tplId = $this->pdfi->importPage(1);
        // use the imported page and place it at point 0,0
        $this->pdfi->useTemplate($tplId, 0, 0);
        $this->pdfi->SetFillColor(255, 255, 255);
        $this->pdfi->SetFont("Arial", "", 7);
        //FOR SENDER NAME & ADDRESS
        // $this->pdfi->SetFont("Arial", "B", 6);
        // $this->pdfi->RotatedText(60.7, 26.2, 'Sender:', 0);
        // $this->pdfi->SetFont("Arial", "B", 6);
        // $this->pdfi->RotatedText(60.7, 28, $this->order->getSenderFullName(), 0);
        // $this->pdfi->SetFont("Arial", "", 6);
        // $this->pdfi->RotatedText(60.7, 30, "2200 NW 129TH AVE", 0);
        // $this->pdfi->SetFont("Arial", "", 6);
        // $this->pdfi->RotatedText(60.7, 32, "United States 33182 Miami FL", 0);
        //FOR CPF#
        if ($this->order->shippingService->service_sub_class == ShippingService::Post_Plus_EMS) {

            if ($this->order->recipient->tax_id) {
                $this->pdfi->SetFont("Arial", "B", 7);
                $this->pdfi->RotatedText(186, 39.7, 'CPF: ' . $this->order->recipient->tax_id, 0);
            }
        }
        //FOR SHIPPING
        $this->pdfi->SetFillColor(255, 255, 255);
        $this->pdfi->Rect($sbw, $sbh, 9.3, 2.5, "F");
        $this->pdfi->SetFont("Arial", "", 5);
        $this->pdfi->RotatedText($sw, $sh, 'Shipping', 0);
        $this->pdfi->RotatedText($tvw, $tvh, 'Total value', 0);
        //FOR SHIPPING COST
        $this->pdfi->SetFont("Arial", "B", 5);
        $userDeclaredFreight = $this->order->user_declared_freight <= 0.01 ? 0 : $this->order->user_declared_freight;
        $this->pdfi->RotatedText($cw, $ch, number_format($userDeclaredFreight, 2, '.', ','), 0);
        //FOR TOTAL ORDER VALUE
        $this->pdfi->SetFillColor(255, 255, 255);
        $this->pdfi->Rect($bw, $bh, 8, 2.5, "F");
        $this->pdfi->SetFont("Arial", "B", 5);
        $this->pdfi->RotatedText($fw, $fh, number_format($userDeclaredFreight + $this->order->order_value, 2, '.', ','), 0);
        //FOR REFERENCE NO
        // if ($this->order->customer_reference || $order->tracking_id) {
        //     $this->pdfi->SetFont("Arial", "B", 7);
        //     $this->pdfi->RotatedText($rw, $rh, 'Ref#: ' . ($this->order->customer_reference ? $this->order->customer_reference : $this->order->tracking_id).' HD-'.$this->order->id, 0);
        // } 
    }
}
