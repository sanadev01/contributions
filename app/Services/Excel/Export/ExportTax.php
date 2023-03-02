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
        $this->prepareExcelSheet();

        return $this->download();
    }

    private function prepareExcelSheet()
    {
        $this->setExcelHeaderRow();

        $row = $this->currentRow;

        foreach ($this->taxes as $tax) {
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
            $this->setCellValue('J'.$row, $tax->adjustment);
            $this->setCellValue('K'.$row, $tax->created_at);
            $row++;
        }

        $this->currentRow = $row;

        $this->setCellValue('D'.$row, "=SUM(D1:D{$row})");
        $this->setCellValue('E'.$row, "=SUM(E1:E{$row})");
        $this->setCellValue('F'.$row, "=SUM(F1:F{$row})");
        $this->setCellValue('G'.$row, "=SUM(G1:G{$row})");
        $this->setCellValue('H'.$row, "=SUM(H1:H{$row})");
        $this->setCellValue('I'.$row, "=SUM(I1:I{$row})");
        $this->setCellValue('J'.$row, "=SUM(J1:J{$row})"); 
        
        $this->setBackgroundColor("A{$row}:K{$row}", 'adfb84');
    }

    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'User Name');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'Warehouse No');
        
        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'Tracking Code');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'Tax Payment (R$)');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', '(Buying) Rate (R$)');
        
        $this->setColumnWidth('F', 20);
        $this->setCellValue('F1', '(Selling) Rate (R$) ');

        $this->setColumnWidth('G', 20);
        $this->setCellValue('G1', '(Buying) (USD) ');

        $this->setColumnWidth('H', 20);
        $this->setCellValue('H1', '(Selling) (USD)');

        $this->setColumnWidth('I', 20);
        $this->setCellValue('I1', 'Profit USD');

        $this->setColumnWidth('J', 20);
        $this->setCellValue('J1', 'Adjustment');

        $this->setColumnWidth('K', 20);
        $this->setCellValue('K1', 'Date');

        $this->setBackgroundColor('A1:K1', '2b5cab');
        $this->setColor('A1:K1', 'FFFFFF');

        $this->currentRow++;
    }
}
