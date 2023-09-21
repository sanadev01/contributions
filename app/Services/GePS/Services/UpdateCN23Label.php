<?php
namespace App\Services\GePS\Services;

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
        $this->pdfi->SetFont("Arial", "", 7);
        $this->pdfi->SetFillColor(255, 255, 255);
        $this->pdfi->Rect(16, 38, 18, 30, "F");
        $this->pdfi->SetFont("Arial", "B", 8);  
        // $this->pdfi->RotatedText(21, 66.7, $this->order->getSenderFullName(), 90);
        $this->pdfi->RotatedText(19, 61.2, "- DEVOLUCAO -", 90);
        $this->pdfi->SetFont("Arial", "", 6);
        // $this->pdfi->RotatedText(23.3, 66.7, $this->order->sender_email, 90);
        $this->pdfi->RotatedText(21.2, 66.7, "(em caso de nao entrega encaminhar para)", 90);
        $this->pdfi->SetFont("Arial", "", 6);
        $this->pdfi->RotatedText(24, 66.7, "Blue Line Ag. De Cargas Ltda.", 90);
        $this->pdfi->SetFont("Arial", "", 6);
        $this->pdfi->RotatedText(26.9, 66.7, "Rua Barao Do Triunfo, 520-CJ 152- Brooklin", 90);
        $this->pdfi->SetFont("Arial", "", 6);
        $this->pdfi->RotatedText(29.9, 66.7, "Paulista CEP 04602-001 - Sao Paulo - SP- Brasil", 90);
        $this->pdfi->SetFont("Arial", "", 6);
        if($this->order->shippingService->service_sub_class == ShippingService::GePS){
            $this->pdfi->RotatedText(99.7, 14, "- LX (Prime)", 90);
        }elseif($this->order->shippingService->service_sub_class == ShippingService::Parcel_Post) {
            $this->pdfi->RotatedText(99.7, 14.5, "- CA Service", 90);
        }

        $this->pdfi->Output($this->pdf_file, 'F');
        return true;
    }
}
