<?php

namespace App\Services\Excel\Export;

use App\Models\Order;

class ExportGDEInvoice extends AbstractExportService
{
    private $row = 1;
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

    private function prepareExcelSheet()
    {
        $senderAddress = $this->order->sender_address. ' - '.$this->order->sender_city.' - '.optional($this->order->senderState)->code; 
        
        $this->setCellValue('A1', 'COMMERCIAL INVOICE');
        $this->setCellValue('A2', 'Shipper/Exporter: (SENDER )');
        $this->setCellValue('A3', "CORREIOS - EMPRESA BRASILEIRA DE CORREIOS E TELÉGRAFOS \n".$senderAddress
            ." \n CEP:". $this->order->user->zipcode."\nCNPJ:".$this->order->user->tax_id
        );
        $this->setCellValue('D2', 'Invoice Number and Date:');  
        $this->setCellValue('G2', optional($this->order->order_date)->format('m/d/Y')); 
        $this->setCellValue('D3', 'USER CUSTOMER REFERENCE'); 
        $this->setCellValue('G3',  $this->order->customer_reference);
        $this->setCellValue('D5', 'Country of Origin:'); 
        $this->setCellValue('G5', $this->order->senderCountry->name);
        $this->setCellValue('D6', 'Country of Destination:');
        $this->setCellValue('G6', $this->order->recipient->country->name);
        $this->setCellValue('A7', 'Consignee/Importer: (RECEIVER)');
        $this->setCellValue('A8', $this->order->recipient->getFullName()."\n".$this->order->recipient->address."\n".
            $this->order->recipient->country->name.', '.$this->order->recipient->state->code.', '.$this->order->recipient->zipcode
        );
        $this->setCellValue('D7', 'Payment Terms:');
        $this->setCellValue('D8', 'AT SIGHT');
        $this->setCellValue('D10', 'Incoterms:');
        $this->setCellValue('D11', 'DDP - Miami');
        $this->setCellValue('A13', 'Payment Terms:');
        $this->setCellValue('B13', "Code (P/N) (USER REFERENCE OF CUSTOMER)");
        $this->setCellValue('C13', 'Description of Goods');
        $this->setCellValue('D13', 'NCM');
        $this->setCellValue('D14', '(HS Code) (10 DIGITS)');
        $this->setCellValue('E13', 'Quantity');
        $this->setCellValue('H13', 'Unit Value (USD)');
        $this->setCellValue('I13', 'Total Value (USD)');

        $this->setExcelformat();
        $this->itemsRow();
        $this->setItemFooter();
        $this->signatureRow();
    }

    private function setExcelformat()
    {
        $this->setBold('A1', true);
        $this->setBold('D2', true);
        $this->setBold('D5:D7', true);
        $this->setBold('D10', true);
        $this->setBold('A2', true);
        $this->setBold('A7', true);
        $this->setBold('D3', true);
        $this->setBold('B13', true);
        $this->setBold('A13:I14', true);

        $this->mergeCells('A1:I1');
        $this->mergeCells('A2:C2');        
        $this->mergeCells('A3:C6');
        $this->mergeCells('G2:I2');
        $this->mergeCells('G3:I3');
        $this->mergeCells('G5:I5');
        $this->mergeCells('G6:I6');
        $this->mergeCells('D2:F2');
        $this->mergeCells('D3:F3');
        $this->mergeCells('D5:F5');
        $this->mergeCells('D6:F6');
        $this->mergeCells('A7:C7');
        $this->mergeCells('A8:C11');
        $this->mergeCells('D7:I7');
        $this->mergeCells('D8:I8');
        $this->mergeCells('D10:I10');
        $this->mergeCells('D11:I11');
        $this->mergeCells('A12:I12');
        $this->mergeCells('A13:A14');
        $this->mergeCells('B13:B14');
        $this->mergeCells('C13:C14');
        $this->mergeCells('E13:G14');
        $this->mergeCells('H13:H14');
        $this->mergeCells('I13:I14');

        $this->setAlignment('A1','center');
        $this->setAlignment('A2','center');
        $this->setAlignment('A13:I14','center');

        $this->setTextWrap('A3:C6', true); 
        $this->setTextWrap('A8:C11',true);
        $this->setTextWrap('A13:I14', true);

        $this->setColumnWidth('A', 10);
        $this->setColumnWidth('B', 20);
        $this->setColumnWidth('C', 50);
        $this->setColumnWidth('D', 15);

        $this->setBorder(['A13:I14'], false);
        $this->setBorder(['A2:C6', 'A1:I1', 'D2:I3', 'D4:I6', 'A7:C11', 'D7:I9', 'D10:I11', 'A12:I12'], false);
    }

    public function itemsRow()
    {
        $row =  15;
        foreach ($this->order->items as $key => $item) {
            $this->setCellValue('A'.$row, $key + 1);
            $this->setCellValue('B'.$row, $this->order->customer_reference);
            $this->setCellValue('C'.$row, $item->description);
            $this->setCellValue('D'.$row, $item->sh_code);
            $this->setCellValue('E'.$row, $item->quantity);
            $this->setCellValue('F'.$row, $this->order->getWeight() / count($this->order->items));
            $this->setCellValue('G'.$row, "kg");
            $this->setCellValue('H'.$row,  $this->order->order_value / count($this->order->items));
            $this->setCellValue('I'.$row,   "$ ".$this->order->order_value / count($this->order->items));
            $this->setBorder(['A'.$row.':I'.$row], false);
            $row++;
        }

        $this->setCellValue('E'.$row, $this->order->items->sum('quantity'));
        $this->setCellValue('F'.$row, $this->order->getWeight());
        $this->setCellValue('G'.$row, "kg");
        $this->setCellValue('H'.$row, $this->order->order_value);
        $this->setCellValue('I'.$row, "$ ".$this->order->order_value);
        $this->setBackgroundColor("E{$row}:F{$row}", 'fff');
        $this->setBackgroundColor("I{$row}", 'fff');
        $this->setBorder(['A'.$row.':I'.$row], false);
        $this->row = $row++;
    }

    public function setItemFooter()
    {
        $row = $this->row;
        $this->mergeCells('F'.$row.':H'.$row);
        $this->mergeCells('F'.($row + 1).':H'.($row + 1));
        $this->mergeCells('F'.($row + 2).':H'.($row + 2));
        $this->mergeCells('F'.($row + 3).':H'.($row + 3));

        $this->setCellValue('F'.$row, 'Value of Goods: ');
        $this->setCellValue('I'.($row), '$ 0');
        $this->setCellValue('F'.($row + 1), 'International Freight: ');
        $this->setCellValue('I'.($row + 1), '$ 0');
        $this->setCellValue('F'.($row + 2), 'Other Charges: ');
        $this->setCellValue('I'.($row + 2), '$ 0');
        $this->setCellValue('F'.($row + 3), 'Total');
        $this->setCellValue('I'.($row + 3), '$ '.$this->order->order_value);
        $this->setBold('F'.$row.':H'.($row + 4).$row, true);
        $this->row = $row + 4;
    }

    public function signatureRow()
    {
        $row = $this->row;
        $this->setCellValue('D'.$row, 'Signature'); 
        $this->mergeCells('F'.($row).':I'.$row);
        $this->setBold('D'.$row, true);
        $this->setBorder(['A'.($row - 5).':I'.($row)], false);
        $this->setBorder(['F'.($row - 2).':I'.($row - 2), 'F'.($row).':I'.$row], true);
    }
}