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
        $cost = null;
        foreach ($this->rates as $rate) {
            
            $this->setCellValue('A'.$row, $rate['weight'] );
            if($cost){
                $this->setCellValue('B'.$row, $cost);
            }else{
                $this->setCellValue('B'.$row, $rate['leve'] );
            }
            $this->setCellValue('C'.$row, "=ROUND(((D$row - B$row) * 100) / B$row,2)");
            $this->setCellValue('D'.$row, 10);
            $row++;
            $cost = $rate['leve'];
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

        $this->setBackgroundColor('A1:D1', '2b5cab');
        $this->setColor('A1:D1', 'FFFFFF');
        $this->currentRow++;
    }
}
