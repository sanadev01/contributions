<?php

namespace App\Services\Excel\Export;

use Illuminate\Support\Collection;

class ProfitPackageRateExport extends AbstractExportService
{
    private $rates;

    private $currentRow = 1;

    public function __construct(Collection $rates)
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
            $this->setCellValue('A'.$row, $rate['weight'] . ' g');
            $this->setCellValue('B'.$row, $rate['profit'] );
            $this->setCellValue('C'.$row, $rate['shipping'][0] );
            $this->setCellValue('D'.$row, "=B$row*(C$row/100)+B$row");
            $row++;
        }
        $this->currentRow = $row;
    }

    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'weight');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'profit');
        
        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'shipping');
        
        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'rates');

        $this->setBackgroundColor('A1:D1', '2b5cab');
        $this->setColor('A1:D1', 'FFFFFF');

        $this->currentRow++;
    }
}
