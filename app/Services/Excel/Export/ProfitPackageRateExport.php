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
            $this->setCellValue('B'.$row, $rate['value'] . ' %');
            $this->setCellValue('C'.$row, $rate['rates'][0] . ' $');
            $row++;
        }
        $this->currentRow = $row;
    }

    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'Weight');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'Profit');
        
        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'Rate');

        $this->setBackgroundColor('A1:C1', '2b5cab');
        $this->setColor('A1:C1', 'FFFFFF');

        $this->currentRow++;
    }
}
