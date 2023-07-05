<?php

namespace App\Services\Excel\Export;

use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Models\Order;
use PhpOffice\PhpSpreadsheet\Style\Color;

class ExportGDEInvoice extends AbstractExportService
{
    private $currentRow = 1;
    private $order;
    public function __construct(Order $order)
    {
        $this->order = $order;
        parent::__construct();
    }
    public function handle()
    {
        $this->prepareExcelSheet();
        return $this->downloadExcel();
    }
    public function superHeadingRow()
    {
        $this->mergeCells('A1:I1');
        $this->sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        $this->sheet->getStyle('A1')->getAlignment()->setVertical('center');
        $this->setCellValue('A1', 'COMMERCIAL INVOICE');
        $this->sheet->getStyle('A1')->applyFromArray(
            [
                'font' => [
                    'bold' => true,
                    'color' => [
                        'rgb' => '000000'
                    ],
                    'size' => 10,
                    'name' => 'Verdana'
                ]
            ]
        );
    }
    public function senderRow()
    {
        // Shipper/Exporter: (SENDER )
        $this->mergeCells('A2:C2');
        $this->sheet->getStyle('A2')->getAlignment()->setHorizontal('center');
        $this->sheet->getStyle('A2')->getAlignment()->setVertical('center');
        $this->setCellValue('A2', 'Shipper/Exporter: (SENDER )'); 
        $this->setStyle(['A2','D2','D5','D6','A7','D7','D10'],'000000',10,'Verdana');
        // address

        $sender_address = $this->order->sender_address ?? 'RUA MERGENTHALER 592 - BAIRRO: VILA LEOPOLDINAs ';
        $sender_address  = $sender_address . ' - ' . $this->order->sender_city ?? 'Sao Paulo';
        $sender_address  = $sender_address . ' - ' . ($this->order->sender_state_id ? $this->order->senderState->code : 'SP'); 

        $this->mergeCells('A3:C6');
        $this->sheet->getStyle('A3:C6')->getAlignment()->setWrapText(true); 
        $this->setCellValue('A3', "CORREIOS - EMPRESA BRASILEIRA DE CORREIOS E TELÉGRAFOS \n".$sender_address." \n CEP:". ($this->order->user->zipcode ?? '05311030') . ' - BRASIL '."\nCNPJ:" . $this->order->user->tax_id ?? '34.028.316/0031-29');

    }
    public function numberDateRow()
    {
        // Invoice Number and Date:
        $this->mergeCells('D2:F2');
        $this->setCellValue('D2', 'Invoice Number and Date:');  
        // DATE 
        $this->setCellValue('H2', 'DATE'); 
        // Invoice Number and Date:
        $this->mergeCells('D3:F3');
        $this->setCellValue('D3', 'USER CUSTOMER REFERENCE'); 
        $this->setStyle(['D3'],'ff0000',10,'Verdana');

        $this->setCellValue('H3',  date('M-d-Y'));


        // Country of Origin:
        $this->mergeCells('D5:F5');
        $this->setCellValue('D5', 'Country of Origin:'); 
        $this->setCellValue('G5', $this->order->senderCountry->name);
        // Country of Destination:
        $this->mergeCells('D6:F6');
        $this->setCellValue('D6', 'Country of Destination:');
      
        $this->setCellValue('G6', $this->order->recipient->country->name);
    } 
    public function receiverRow()
    {
        // Shipper/Exporter: (SENDER )
        $this->mergeCells('A7:C7');
        $this->setCellValue('A7', 'Consignee/Importer: (RECEIVER)'); 
        // address
        $this->mergeCells('A8:C11');  
        
        $this->sheet->getStyle('A8:C11')->getAlignment()->setVertical('center');
        $this->sheet->getStyle('A8:C11')->getAlignment()->setWrapText(true); 
        $this->setCellValue('A8', $this->order->recipient->getFullName()."\n".$this->order->recipient->address."\n".$this->order->recipient->country->name . ' , ' . $this->order->recipient->state->code . ' , ' . $this->order->recipient->zipcode);
    }
    public function paymentTermRow()
    {
        $this->mergeCells('D7:I7');
        $this->setCellValue('D7', 'Payment Terms:');
      
        $this->mergeCells('D8:I8');
        $this->setCellValue('D8', 'AT SIGHT');


        $this->mergeCells('D10:I10');
        $this->setCellValue('D10', 'Incoterms:');
       
        $this->mergeCells('D11:I11');
        $this->mergeCells('A12:I12');
        $this->setCellValue('D11', 'DDP - Miami');
    }
    public function itemsRow()
    {

        $this->setItemHeaderRow();
        $this->currentRow =  15;

        foreach ($this->order->items as $key => $item) {
            $this->setCellValue('A' . $this->currentRow, $key + 1);
            $this->setCellValue('B' . $this->currentRow, $this->order->customer_reference);
            $this->setCellValue('C' . $this->currentRow, $item->description);
            $this->setCellValue('D' . $this->currentRow, $item->sh_code);
            $this->setCellValue('E' . $this->currentRow, $item->quantity);
            $this->setCellValue('F' . $this->currentRow, $this->order->getWeight() / count($this->order->items));
            $this->setCellValue('G' . $this->currentRow, "kg");
            $this->setCellValue('H' . $this->currentRow,  $this->order->order_value / count($this->order->items));
            $this->setCellValue('I' . $this->currentRow,   "$ " . $this->order->order_value / count($this->order->items));
            $this->applyBorder(['A' . $this->currentRow . ':I' . $this->currentRow], Border::BORDER_THIN, new Color('000'));
            $this->currentRow++;
        }
        $this->setCellValue('E' . $this->currentRow, $this->order->items->sum('quantity'));
        $this->setCellValue('F' . $this->currentRow, $this->order->getWeight());
        $this->setCellValue('G' . $this->currentRow, "kg");
        $this->setCellValue('H' . $this->currentRow, $this->order->order_value);
        $this->setCellValue('I' . $this->currentRow, "$ " . $this->order->order_value);
        $this->setBackgroundColor("E{$this->currentRow}:F{$this->currentRow}", 'ffff00');
        $this->setBackgroundColor("I{$this->currentRow}", 'ffff00');
        //borders 
        $this->applyBorder(['A' . $this->currentRow . ':I' . $this->currentRow], Border::BORDER_THIN, new Color('000'));
        $this->currentRow++;
        $this->setItemFooter();
    }
    public function setItemHeaderRow()
    {
        $this->mergeCells('A13:A14');
        $this->setCellValue('A13', 'Payment Terms:');
        $this->setColumnWidth('A', 10);
        $this->setColumnWidth('B', 20);
        $this->mergeCells('B13:B14');
        $this->setCellValue('B13', "Code (P/N) (USER REFERENCE OF CUSTOMER)");
        $this->setColumnWidth('C', 50);
        $this->mergeCells('C13:C14');
        $this->setCellValue('C13', 'Description of Goods');
        $this->setColumnWidth('D', 15);
        $this->setCellValue('D13', 'NCM');
        $this->setCellValue('D14', '(HS Code) (10 DIGITS)');
        $this->mergeCells('E13:G14');
        $this->setCellValue('E13', 'Quantity');
        $this->setCellValue('H13', 'Unit Value');
        $this->setCellValue('H14', '(USD)');
        $this->setCellValue('I13', 'Total Value');
        $this->setCellValue('I14', '(USD)');
        $this->sheet->getStyle('A13:I14')->getAlignment()->setHorizontal('center');
        $this->sheet->getStyle('A13:I14')->getAlignment()->setVertical('center');
        $this->sheet->getStyle('A13:I14')->getAlignment()->setWrapText(true); 
        $this->setStyle(['A13:I14'],'000000',9,'Verdana');
        $this->setStyle(['B13'],'ff0000',9,'Verdana'); 
        //borders 
        $this->applyBorder(['A13:I14'], Border::BORDER_THIN, new Color('000'));
        $this->currentRow++;
    }
    public function setItemFooter()
    {
        $this->mergeCells('G' . $this->currentRow . ':H' . $this->currentRow);
        $this->setCellValue('G' . $this->currentRow, 'Value of');
        $this->setCellValue('I' . ($this->currentRow), '$ 0');
        $this->mergeCells('G' . ($this->currentRow + 1) . ':H' . ($this->currentRow + 1));
        $this->setCellValue('G' . ($this->currentRow + 1), 'International');
        $this->setCellValue('I' . ($this->currentRow + 1), '$ 0');
        $this->mergeCells('G' . ($this->currentRow + 2) . ':H' . ($this->currentRow + 2));
        $this->setCellValue('G' . ($this->currentRow + 2), 'Others');
        $this->setCellValue('I' . ($this->currentRow + 2), '$ 0');
        $this->mergeCells('G' . ($this->currentRow + 3) . ':H' . ($this->currentRow + 3));
        $this->setCellValue('G' . ($this->currentRow + 3), 'Total');
        $this->setCellValue('I' . ($this->currentRow + 3), '$ ' . $this->order->order_value);
        $this->setStyle(['G' . $this->currentRow . ':H' . ($this->currentRow + 4)],'000000',9,'Verdana'); 
        $this->currentRow = $this->currentRow + 4;
    }
    public function signatureRow()
    {
        $this->setCellValue('D' . $this->currentRow, 'Signature'); 
        $this->setStyle(['D' . $this->currentRow],'000000',9,'Verdana'); 
        $this->applyBorder(['A' . ($this->currentRow - 5) . ':I' . ($this->currentRow)], Border::BORDER_THIN, new Color('000'));
        $this->mergeCells('F' . ($this->currentRow) . ':I' . $this->currentRow);
        $this->applyBottomBorder(['F' . ($this->currentRow - 2) . ':I' . ($this->currentRow - 2), 'F' . ($this->currentRow) . ':I' . $this->currentRow], Border::BORDER_THICK, new Color('F00'));
    }
    public function setBorderRow()
    {
        $this->applyBorder(['A2:C6', 'A1:I1', 'D2:I3', 'D4:I6', 'A7:C11', 'D7:I9', 'D10:I11', 'A12:I12'], Border::BORDER_THIN, new Color('000'));
    }
    private function prepareExcelSheet()
    {
        $this->superHeadingRow();
        $this->senderRow();
        $this->numberDateRow();
        $this->receiverRow();
        $this->paymentTermRow();
        $this->itemsRow();
        $this->signatureRow();
        $this->setBorderRow();
    }
}
