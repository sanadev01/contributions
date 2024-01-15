<?php

namespace App\Services\Excel\Export;

use App\Models\ProfitPackage;
use App\Models\ProfitSetting;
use Illuminate\Support\Collection;

class ExportShCodes extends AbstractExportService
{
    private $shCodes;

    private $currentRow = 1;

    public function __construct(Collection $shCodes)
    {
        $this->shCodes = $shCodes;

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

        foreach ($this->shCodes as $shCode) {
            $description = explode('-------',$shCode->description);
            $this->setCellValue('A'.$row, $shCode->code);
            $this->setCellValue('B'.$row, optional($description)[0]);
            $this->setCellValue('C'.$row, optional($description)[1]);
            $this->setCellValue('D'.$row, optional($description)[2]);
            $this->setCellValue('E'.$row, $shCode->type);
            $row++;
        }

        $this->currentRow = $row;
    }

    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'Code');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'English');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'Portuguese');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'Spanish');
        
        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'Spanish');

        $this->setBackgroundColor('A1:E1', '2b5cab');
        $this->setColor('A1:E1', 'FFFFFF');

        $this->currentRow++;
    }
}
