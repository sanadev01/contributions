<?php

namespace App\Services\Excel\Export;

use Illuminate\Support\Collection;

class ExportTax extends AbstractExportService
{
    private $taxes;

    private $currentRow = 1;

    public function __construct(Collection $taxes)
    {
        $this->taxes = $taxes;

        parent::__construct();
    }

    public function handle()
    {
        $this->prepareExcelSheetTax();
        $this->setCellValue('A'.$this->currentRow, 'Refunded');
        $row = 'A'.$this->currentRow.':K'.$this->currentRow;
        $this->mergeCells($row);
        $this->setBackgroundColor($row, '000000');
        $this->setColor($row, 'FFFFFF'); 

        $this->currentRow++;
        $this->prepareExcelSheetRefund();
        
        $this->setCellValue('A'.$this->currentRow, 'Adjustment');
        $row = 'A'.$this->currentRow.':C'.$this->currentRow;
        $this->mergeCells($row);
        $this->setBackgroundColor($row, '000000');
        $this->setColor($row, 'FFFFFF'); 
        

        $this->currentRow++;


        $this->prepareExcelSheetAdjustment();

        return $this->download();
    }

    private function prepareExcelSheetTax()
    {

        $this->setExcelHeaderRow($this->currentRow);
        $row = $this->currentRow;
        foreach ($this->taxes as $tax) {
            if(optional($tax->deposit)->last_four_digits != 'Tax refunded' && $tax->adjustment==null)
            {
            $user = $tax->user;
            $order = $tax->order;
            $this->setCellValue('A'.$row, $user->name);
            $this->setCellValue('B'.$row, optional($order)->warehouse_number);
            $this->setCellValue('C'.$row, optional($order)->corrios_tracking_code);
            $this->setCellValue('D'.$row, $tax->tax_payment);   
            $this->setCellValue('E'.$row, $tax->buying_br);
            $this->setCellValue('F'.$row, $tax->selling_br);
            $this->setCellValue('G'.$row, $tax->buying_usd);
            $this->setCellValue('H'.$row, $tax->selling_usd);
            $this->setCellValue('I'.$row, round($tax->selling_usd - $tax->buying_usd,2));
            $this->setCellValue('J'.$row, optional($tax->deposit)->description);
            $this->setCellValue('K'.$row, $tax->created_at);
            $row++;
            }

        }
 

        $this->setCellValue('D'.$row, "=SUM(D1:D{$row})");
        $this->setCellValue('E'.$row, "=SUM(E1:E{$row})");
        $this->setCellValue('F'.$row, "=SUM(F1:F{$row})");
        $this->setCellValue('G'.$row, "=SUM(G1:G{$row})");
        $this->setCellValue('H'.$row, "=SUM(H1:H{$row})");
        $this->setCellValue('I'.$row, "=SUM(I1:I{$row})");
        
        $this->setBackgroundColor("A{$row}:K{$row}", 'adfb84');
        $this->currentRow = ++$row;

    }

    
    private function prepareExcelSheetRefund()
    {

        $this->setExcelHeaderRow($this->currentRow);
        $refundStartAt = $row = $this->currentRow;

        foreach ($this->taxes as $tax) {
            if(optional($tax->deposit)->last_four_digits == 'Tax refunded' && $tax->adjustment==null)
            {
            $user = $tax->user;
            $order = $tax->order;
            $this->setCellValue('A'.$row, $user->name);
            $this->setCellValue('B'.$row, optional($order)->warehouse_number);
            $this->setCellValue('C'.$row, optional($order)->corrios_tracking_code);
            $this->setCellValue('D'.$row, $tax->tax_payment);   
            $this->setCellValue('E'.$row, $tax->buying_br);
            $this->setCellValue('F'.$row, $tax->selling_br);
            $this->setCellValue('G'.$row, $tax->buying_usd);
            $this->setCellValue('H'.$row, $tax->selling_usd);
            $this->setCellValue('I'.$row, round($tax->selling_usd - $tax->buying_usd,2));
            $this->setCellValue('J'.$row,  optional($tax->deposit)->description);
            $this->setCellValue('K'.$row, $tax->created_at);
            $row++;
            }

        }

        $this->setCellValue('D'.$row, "=SUM(D".$refundStartAt.":D{$row})");
        $this->setCellValue('E'.$row, "=SUM(E".$refundStartAt.":E{$row})");
        $this->setCellValue('F'.$row, "=SUM(F".$refundStartAt.":F{$row})");
        $this->setCellValue('G'.$row, "=SUM(G".$refundStartAt.":G{$row})");
        $this->setCellValue('H'.$row, "=SUM(H".$refundStartAt.":H{$row})");
        $this->setCellValue('I'.$row, "=SUM(I".$refundStartAt.":I{$row})");
        
        $this->setBackgroundColor("A{$row}:K{$row}", 'adfb84');
        $this->currentRow =  ++$row;
    }

    
    
    private function prepareExcelSheetAdjustment()
    {

        $this->setAdjustmentExcelHeaderRow($this->currentRow);
        $adjustmentdStartAt  = $row = $this->currentRow;


        foreach ($this->taxes as $tax) {
            if($tax->adjustment!=null)
            {
            $user = $tax->user;
            $this->setCellValue('A'.$row, $user->name);
            $this->setCellValue('B'.$row, $tax->adjustment);
            $this->setCellValue('C'.$row, '  '.$tax->created_at);
            $row++;
            }

        }
 
        $this->setCellValue('B'.$row, "=SUM(B". $adjustmentdStartAt.":B{$row})"); 
        
        $this->setBackgroundColor("A{$row}:C{$row}", 'adfb84');
        $this->currentRow = $row;
    }

    private function setExcelHeaderRow($row)
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A'.$row, 'User Name');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B'.$row, 'Warehouse No');
        
        $this->setColumnWidth('C', 20);
        $this->setCellValue('C'.$row, 'Tracking Code');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D'.$row, 'Tax Payment (R$)');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E'.$row, '(Buying) Rate (R$)');
        
        $this->setColumnWidth('F', 20);
        $this->setCellValue('F'.$row, '(Selling) Rate (R$) ');

        $this->setColumnWidth('G', 20);
        $this->setCellValue('G'.$row, '(Buying) (USD) ');

        $this->setColumnWidth('H', 20);
        $this->setCellValue('H'.$row, '(Selling) (USD)');

        $this->setColumnWidth('I', 20);
        $this->setCellValue('I'.$row, 'Profit USD');

        $this->setColumnWidth('J', 30);
        $this->setCellValue('J'.$row, 'Reason');

        $this->setColumnWidth('K', 20);
        $this->setCellValue('K'.$row, 'Date');

        $this->setBackgroundColor('A'.$row.':K'.$row, '2b5cab');
        $this->setColor('A'.$row.':K'.$row, 'FFFFFF');

        $this->currentRow++;
    }

    private function setAdjustmentExcelHeaderRow($row)
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A'.$row, 'User Name');
 

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B'.$row, 'Adjustment');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C'.$row, 'Date');

        $this->setBackgroundColor('A'.$row.':C'.$row, '2b5cab');
        $this->setColor("A{$row}:C".$row, 'FFFFFF');

        $this->currentRow++;
    }
}
