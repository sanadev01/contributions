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
        $this->pdfi->Rect(143, 22, 7, 2, "F");
        // $this->pdfi->RotatedText(143, 24, ' ', 0);
        
        $this->pdfi->SetFillColor(255, 255, 255);
        $this->pdfi->Rect(143, 27.5, 7, 4, "F");
        // $userDeclaredFreight = $this->order->user_declared_freight <= 0.01 ? 0 : $this->order->user_declared_freight;
        // $this->pdfi->RotatedText(143, 29.7, ' ', 0);

        $this->pdfi->SetFillColor(255, 255, 255);
        $this->pdfi->Rect(143, 38.5, 7, 3, "F");
        // $userDeclaredFreight = $this->order->user_declared_freight <= 0.01 ? 0 : $this->order->user_declared_freight;
        // $this->pdfi->RotatedText(143, 41.1, ' ', 0);
       

        $this->pdfi->Output($this->pdf_file, 'F');
        return true;
    }
}
