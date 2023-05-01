<?php

namespace App\Services\USPS\Services;

use App\Models\Order;
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
        $this->pdfi = new PDFRotate('P', 'mm', array(152,130));
    }
    public function run()
    {
        // initiate FPDI
        // $this->pdfi->setPrintHeader(false);
        $this->pdfi->AddPage();
        $this->pdfi->SetMargins(0, 0, 0);
        // set the source file 
        $this->pdfi->setSourceFile($this->pdf_file);
        // import page 1
        $tplId = $this->pdfi->importPage(1);
        // use the imported page and place it at point 0,0
        $this->pdfi->useTemplate($tplId, 0,0,152.3,101.6    ,true);
        $this->pdfi->SetFillColor(255, 255, 255);
        
        foreach($this->order->items  as $key=>$item){
            if($key<5){
                $this->pdfi->SetFont("Arial", "", 7);
                $this->pdfi->RotatedText(95, 29+($key*3.5), $item->sh_code, 0);
            }
        }

        $this->pdfi->SetFillColor(255, 255, 255);
        $this->pdfi->Rect(140, 22, 11, 3, "F");
        // $this->pdfi->RotatedText(140, 24, ' ', 0);
        
        $this->pdfi->SetFillColor(255, 255, 255);
        $this->pdfi->Rect(140, 27.5, 11, 4, "F");
        // $userDeclaredFreight = $this->order->user_declared_freight <= 0.01 ? 0 : $this->order->user_declared_freight;
        // $this->pdfi->RotatedText(140, 29.7, ' ', 0);

        $this->pdfi->SetFillColor(255, 255, 255);
        $this->pdfi->Rect(140, 38.5, 11, 3, "F");
        // $userDeclaredFreight = $this->order->user_declared_freight <= 0.01 ? 0 : $this->order->user_declared_freight;
        // $this->pdfi->RotatedText(140, 41.1, ' ', 0);
       
        $this->updateGDELabel();

        $this->pdfi->Output($this->pdf_file, 'F');
        return true;
    }
    public function updateGDELabel()
    {
        if($this->order->shippingService->isGDEService()){
            
            
            $this->pdfi->SetFillColor(255,255, 255);
            $this->pdfi->Rect(8,72, 45, 13, "F");
            $this->pdfi->SetFont("Arial", "", 7);
            $this->pdfi->RotatedText(10, 75 ,'2200 NW, 129th Ave – Suite # 100', 0); 
            $this->pdfi->RotatedText(10, 78 ,'Miami, FL, 33182', 0); 
            $this->pdfi->RotatedText(10, 81 ,'United States', 0);
            $this->pdfi->SetFillColor(255, 255,255);
            $this->pdfi->Rect(40, 68.5, 21, 4, "F");
            $this->pdfi->RotatedText(40, 71 ,'+13058885191', 0);
        }
        
    }
}
