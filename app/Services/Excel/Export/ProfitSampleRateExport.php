<?php

namespace App\Services\Excel\Export;

class ProfitSampleRateExport extends AbstractExportService
{
    private $rates;

    private $currentRow = 1;

    public function __construct($rates)
    {
        $this->rates = $rates;

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
        foreach ($this->rates as $rate) {
            
            $this->setCellValue('A'.$row, $rate['weight'] );
            $this->setCellValue('B'.$row, $rate['leve'] );
            $this->setCellValue('C'.$row, "=((D$row - B$row) * 100) / B$row");
            $this->setCellValue('D'.$row, 10);
            $row++;
        }
        $this->currentRow = $row;
    }

    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'Weight');
        
            $this->setColumnWidth('B', 20);
            $this->setCellValue('B1', 'Cost');
            
            $this->setColumnWidth('C', 20);
            $this->setCellValue('C1', 'Profit');
            
            $this->setColumnWidth('D', 20);
            $this->setCellValue('D1', 'Selling Rates');
        $this->currentRow++;
    }
}
