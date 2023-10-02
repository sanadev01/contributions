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
        if($this->order->shippingService->service_sub_class == ShippingService::Post_Plus_Registered) {
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
            // FOR RETURN ADDRESS
            $paddingLeft = 60.6;
            $font = 5;
            $this->printReturnAddress($paddingLeft, 6.0, 35, 11.8, 5.5, 8, 10.5, 13, $font, 'B');

            if(!app()->isProduction()){
                //FOR SENDER ADDRESS
                $this->printSender($this->order, $paddingLeft, 13.5, 35, 11.8, 16.5, 18.5, 20.5, 22.5, 23, $font, '');
                //FOR REFERENCE
                $this->printReference($this->order, 90.0, 51.0);
            }

            //FOR TOTAL WEIGHT
            $this->printWeight($this->order, 31.0, 64.0);
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
            //FOR SH CODES PRINT
            $this->printNCM($this->order, 5.0, 62.0, 11.0);

            $this->pdfi->Output($this->pdf_file, 'F');
            return true;

        } elseif($this->order->shippingService->service_sub_class == ShippingService::Post_Plus_Prime) {
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
            // FOR RETURN ADDRESS
            $paddingLeft = 57.8;
            $font = 4.5;
            $this->printReturnAddress($paddingLeft, 6.0, 35, 11.8, 5.5, 8, 10.5, 13, $font, 'B');
            
            if(!app()->isProduction()){
                //FOR SENDER ADDRESS
                $this->printSender($this->order, $paddingLeft, 13.5, 35, 11.8, 16.5, 18.5, 20.5, 22.5, 23, $font, '');
                //FOR REFERENCE
                $this->printReference($this->order, 90.0, 51.0);
            }

            //FOR TOTAL WEIGHT
            $this->printWeight($this->order, 31.0, 64.0);
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
            //FOR SH CODES PRINT
            $this->printNCM($this->order, 5.0, 62.0, 11.0);

            $this->pdfi->Output($this->pdf_file, 'F');
            return true;

        } elseif ($this->order->shippingService->service_sub_class == ShippingService::Post_Plus_EMS) {
            if(count($this->order->items) > 5) {
                $this->pdfi = new PDFRotate('P', 'mm', array(212.9, 275));
                $this->printShippingOrderValues(118.7, 212.5, 135.8, 212.5, 136, 215, 12.5, 234.3, 133.1, 213, 118.6, 211.1, 118.7, 215);
                
            }else {
                $this->pdfi = new PDFRotate('L', 'mm', array(147, 212.9));
                $this->printShippingOrderValues(118.7, 92.5, 135.8, 92.5, 136, 95.2, 12.5, 114.7, 133.8, 92.9, 117.8, 91.2, 118.7, 95.3);
            }
            $this->pdfi->SetFont("Arial", "B", 7);
            $this->pdfi->RotatedText(186, 29.2, 'CPF:', 0);
            foreach($this->order->items  as $key=>$item){
                $this->pdfi->SetFont("Arial", "B", 7);
                $this->pdfi->RotatedText(143, 74+($key*4), $item->sh_code, 0);
                $this->pdfi->SetFont("Arial", "B", 7);
                $this->pdfi->RotatedText(176.5, 74+($key*4), 'USA', 0);
            }
            $this->printReturnAddress(60.7, 22, 40, 12, 26, 29, 32, 35, 6, '');
            $this->pdfi->SetFont("Arial", "B", 7);
            if(count($this->order->items) > 5) {
                $this->pdfi->RotatedText(100.2, 218.7, 'X', 0);
            } else {
                $this->pdfi->RotatedText(100.2, 98.7, 'X', 0);
            }
            $this->pdfi->Output($this->pdf_file, 'F');
            return true;

        } elseif($this->order->shippingService->service_sub_class == ShippingService::Post_Plus_Premium) {
            if(count($this->order->items) > 5) {
                $this->pdfi = new PDFRotate('P', 'mm', array(212.9, 275));
                $this->printShippingOrderValues(107.7, 230, 119.8, 230, 118.7, 232, 12.5, 234.3, 117.5, 230.1, 107.3, 228.3, 107.7, 232.2);
                
            }else{
                $this->pdfi = new PDFRotate('L', 'mm', array(147, 212.9));
                $this->printShippingOrderValues(107.7, 92.5, 120.8, 92.5, 121, 95.2, 12.5, 114.7, 116.5, 93.2, 107.3, 91.2, 107.3, 95.2);
            }
            //FOR CPF
            // $this->pdfi->SetFont("Arial", "B", 7);
            // $this->pdfi->RotatedText(186, 29.2, 'CPF:', 0);
            //FOR ITEMS SH CODE
            if(count($this->order->items) > 5) {
                foreach($this->order->items  as $key=>$item){
                    $this->pdfi->SetFont("Arial", "B", 7);
                    $this->pdfi->RotatedText(133, 71+($key*4.5), $item->sh_code, 0);
                    //USA
                    $this->pdfi->SetFont("Arial", "B", 7);
                    $this->pdfi->RotatedText(169, 71+($key*4.5), 'USA', 0);
                }
            } else {
                foreach($this->order->items  as $key=>$item){
                    $this->pdfi->SetFont("Arial", "B", 7);
                    $this->pdfi->RotatedText(133, 71.5+($key*3.7), $item->sh_code, 0);
                    //USA
                    $this->pdfi->SetFont("Arial", "B", 7);
                    $this->pdfi->RotatedText(169, 71.5+($key*3.7), 'USA', 0);
                }
            }
            // FOR RETURN ADDRESS
            $this->printReturnAddress(35.7, 27, 78, 7.2, 28.7, 30.7, 33.1, 35, 6, 'B');
            //FOR BOX CHECK
            $this->pdfi->SetFont("Arial", "B", 7);
            if(count($this->order->items) > 5) {
                $this->pdfi->RotatedText(94, 235.7, 'X', 0);
            } else {
                $this->pdfi->RotatedText(93.8, 98.7, 'X', 0);
            }

            //FOR SENDER PHONE
            $this->pdfi->SetFillColor(255, 255, 255);
            $this->pdfi->Rect(57, 17, 25, 2.5, "F");
            $this->pdfi->SetFont("Arial", "B", 6.5);
            $this->pdfi->RotatedText(56, 19.3, $this->order->sender_phone? $this->order->sender_phone : '' , 00);

            //FOR RECEIVER EMAIL
            $this->pdfi->SetFont("Arial", "B", 6.5);
            $this->pdfi->RotatedText(71, 48.5, $this->order->recipient->email, 00);
            
            //FOR CPF No.
            $this->pdfi->SetFont("Arial", "", 7);
            if(count($this->order->items) > 5) {
                $this->pdfi->RotatedText(11.6, 247.5, 'CPF '.optional($this->order->recipient)->tax_id, 0);
            } else {
                $this->pdfi->RotatedText(11.6, 110.5, 'CPF '.optional($this->order->recipient)->tax_id, 0);
            }

            $this->pdfi->Output($this->pdf_file, 'F');
            
            return true;
        } elseif($this->order->shippingService->service_sub_class == ShippingService::LT_PRIME) {
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
            // FOR RETURN ADDRESS
            $paddingLeft = 57.8;
            $font = 4.5;
            $this->printReturnAddress($paddingLeft, 6.0, 38.8, 11.5, 7.5, 9.0, 10.5, 12.0, $font, 'B');

            //FOR SENDER ADDRESS
            $this->printSender($this->order, $paddingLeft, 13.5, 35, 11.8, 16.5, 18.5, 20.5, 22.5, 23, $font, '');

            //FOR CPF
            $this->printCPF($this->order, 121, 50.0, 108, 46.0, 37, 4.5);

            //FOR SENDER REF
            $this->printSenderRef($this->order, 93, 56.0, 93, 53.0, 52, 5.0);
            
            //FOR SH CODES PRINT
            $this->printNCM($this->order, 5.0, 62.0, 11.0);
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
            //FOR TOTAL WEIGHT
            $this->printWeight($this->order, 31.0, 64.0);
            
            $this->pdfi->Output($this->pdf_file, 'F');
            return true;

        }
        
    }
    
    public function printShippingOrderValues($sw, $sh, $cw, $ch, $fw, $fh, $rw, $rh, $bw, $bh, $sbw, $sbh, $tvw, $tvh) {

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
        //FOR SHIPPING & ORDER TEXT
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
        $this->pdfi->Rect($bw, $bh, 8, 2.2, "F");
        $this->pdfi->SetFont("Arial", "B", 5);
        $this->pdfi->RotatedText($fw, $fh, number_format($userDeclaredFreight + $this->order->order_value, 2, '.', ','), 0);

    }

    public function printReturnAddress($rectLM, $rectLT, $rectH, $rectW, $textLine1H, $textLine2H, $textLine3H, $textLine4H, $fontSize, $fontWeight) {
        $this->pdfi->SetFillColor(255, 255, 255);
        $this->pdfi->Rect($rectLM, $rectLT, $rectH, $rectW, "F");
        $this->pdfi->SetFont("Arial", 'B', $fontSize);
        $this->pdfi->RotatedText($rectLM, $textLine1H, "DEVOLUCAO", 0);
        $this->pdfi->SetFont("Arial", $fontWeight, $fontSize);
        $this->pdfi->RotatedText($rectLM, $textLine2H, "Homedeliverybr", 0);
        if($this->order->shippingService->service_sub_class == ShippingService::Post_Plus_Premium) {
            $this->pdfi->SetFont("Arial", $fontWeight, $fontSize);
            $this->pdfi->RotatedText($rectLM, $textLine3H, "Rua Acaca 47- Ipiranga, Sao Paulo CEP 04201-020", 0);
        }else {
            $this->pdfi->SetFont("Arial", $fontWeight, $fontSize);
            $this->pdfi->RotatedText($rectLM, $textLine3H, "Rua Acaca 47- Ipiranga", 0);
            $this->pdfi->SetFont("Arial", $fontWeight, $fontSize);
            $this->pdfi->RotatedText($rectLM, $textLine4H, "Sao Paulo CEP 04201-020", 0);
        }
    }

    public function printSender($order, $rectLM, $rectLT, $rectH, $rectW, $textLineH, $textLine1H, $textLine2H, $textLine3H, $textLine4H, $fontSize, $fontWeight) {
        $this->pdfi->SetFillColor(255, 255, 255);
        $this->pdfi->Rect($rectLM, $rectLT, $rectH, $rectW, "F");
        $this->pdfi->SetFont("Arial", 'B', 5.5);
        $this->pdfi->RotatedText($rectLM, $textLineH, "Sender:", 0);
        $this->pdfi->SetFont("Arial", $fontWeight, $fontSize);
        $this->pdfi->RotatedText($rectLM, $textLine1H, $order->getSenderFullName(), 0);
        $this->pdfi->SetFont("Arial", $fontWeight, $fontSize);
        $this->pdfi->RotatedText($rectLM, $textLine2H, "2200 NW 129TH AVE", 0);
        $this->pdfi->SetFont("Arial", $fontWeight, $fontSize);
        $this->pdfi->RotatedText($rectLM, $textLine3H, "United States 33182 Miami FL", 0);
    }

    public function printReference($order, $lM, $tH) {
        $this->pdfi->SetFont("Arial", 'B', 7.0);
        $this->pdfi->RotatedText($lM, $tH, 'Ref.No: ' . $order->customer_reference . ' ' . $order->warehouse_number, 0);
    }

    public function printWeight($order, $lM, $tH) {
        $this->pdfi->SetFont("Arial", "B", 5);
        $this->pdfi->RotatedText($lM, $tH, $order->weight, 0);
    }

    public function printNCM($order, $lM, $tH, $lM2) {
        foreach($this->order->items  as $key=>$item){
            $this->pdfi->SetFont("Arial", "B", 5);
            $this->pdfi->RotatedText($lM, $tH+($key*2.3), $item->sh_code, 0);
            $this->pdfi->SetFont("Arial", "B", 5);
            $this->pdfi->RotatedText($lM2, $tH+($key*2.3), ' USA', 0);
        }
        return true;
    }

    public function printCPF($order, $lM, $tH, $rectLM, $rectLT, $rectH, $rectW) {
        $this->pdfi->SetFillColor(255, 255, 255);
        $this->pdfi->Rect($rectLM, $rectLT, $rectH, $rectW, "F");
        $this->pdfi->SetFont("Arial", 'B', 7.5);
        $this->pdfi->RotatedText($lM, $tH, 'CPF: '.optional($this->order->recipient)->tax_id, 0);
    }

    public function printSenderRef($order, $lM, $tH, $rectLM, $rectLT, $rectH, $rectW) {
        $this->pdfi->SetFillColor(255, 255, 255);
        $this->pdfi->Rect($rectLM, $rectLT, $rectH, $rectW, "F");
        $this->pdfi->SetFont("Arial", 'B', 7.5);
        $this->pdfi->RotatedText($lM, $tH, 'Sender ref: '.optional($order)->customer_reference. ' ' . optional($order)->warehouse_number, 0);
    }
}
