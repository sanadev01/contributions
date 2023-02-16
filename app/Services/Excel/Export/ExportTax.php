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

            $this->setCellValue('A'.$row, $user->pobox_number);
            $this->setCellValue('B'.$row, $order->warehouse_number);
            $this->setCellValue('C'.$row, $order->corrios_tracking_code);
            $this->setCellValue('D'.$row, $tax->tax_payment);
            $this->setCellValue('E'.$row, $tax->selling_usd);
            $this->setCellValue('F'.$row, $tax->buying_usd);
            $this->setCellValue('G'.$row, round($tax->selling_usd - $tax->buying_usd,2));
            $this->setCellValue('H'.$row, $tax->created_at);
            $row++;
        }

        $this->currentRow = $row;

        $this->setCellValue('D'.$row, "=SUM(D1:D{$row})");
        $this->setCellValue('E'.$row, "=SUM(E1:E{$row})");
        $this->setCellValue('F'.$row, "=SUM(F1:F{$row})");
        $this->setCellValue('G'.$row, "=SUM(G1:G{$row})");
        
        $this->setBackgroundColor("A{$row}:H{$row}", 'adfb84');
    }

    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'PoBox#');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'Warehouse#');
        
        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'Tracking Code');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'Tax Payment');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'Selling cost');

        $this->setColumnWidth('F', 20);
        $this->setCellValue('F1', 'Buying Cost');

        $this->setColumnWidth('G', 20);
        $this->setCellValue('G1', 'profit');

        $this->setColumnWidth('H', 20);
        $this->setCellValue('H1', 'Date');

        $this->setBackgroundColor('A1:H1', '2b5cab');
        $this->setColor('A1:H1', 'FFFFFF');

        $this->currentRow++;
    }
}
