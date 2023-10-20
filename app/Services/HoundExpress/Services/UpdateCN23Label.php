<?php

namespace App\Services\SwedenPost\Services;

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
        $this->pdfi = new PDFRotate('P', 'mm', array(152, 104));
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
        $this->pdfi->useTemplate($tplId, 0, 0);
        $this->pdfi->SetFillColor(255, 255, 255);
        #######JERSEY 

        #######bill
        $this->pdfi->SetFont("Arial", "", 7);
        $this->pdfi->SetFont("Arial", "B", 5);
        $this->pdfi->RotatedText(50, 147, 'SHIPPING:', 90);

        $this->pdfi->SetFont("Arial", "B", 5);
        $userDeclaredFreight = $this->order->user_declared_freight <= 0.01 ? 0 : $this->order->user_declared_freight;
        $this->pdfi->RotatedText(50, 105, number_format($userDeclaredFreight, 2, '.', ','), 90);

        if ($this->order->recipient->tax_id) {
            $this->pdfi->SetFont("Arial", "B", 8);
            $this->pdfi->RotatedText(61, 89, 'CPF: ' . $this->order->recipient->tax_id, 90);
        }  
        // if ($this->order->warehouse_number) {
        //     $this->pdfi->SetFont("Arial", "B", 8);
        //     $warehouse_number = $this->order->warehouse_number;
        //     $warhouse_length =  strlen($warehouse_number);
        //     $length = $warhouse_length<9?$warhouse_length*1.65:$warhouse_length*1.85; 
        //     $this->pdfi->RotatedText(58, $length , $warehouse_number, 90);
        // }


        $this->pdfi->SetFillColor(255, 255, 255);
        $this->pdfi->Rect(65, 99, 7, 9, "F");
        $this->pdfi->SetFont("Arial", "B", 5);
        $this->pdfi->RotatedText(68, 107, number_format($userDeclaredFreight + $this->order->order_value, 2, '.', ','), 90);


        $this->pdfi->Output($this->pdf_file, 'F');
        return true;
    }
}
