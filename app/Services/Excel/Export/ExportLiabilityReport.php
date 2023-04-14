<?php

namespace App\Services\Excel\Export;

use App\Models\Order;
use App\Models\ShippingService;
use Illuminate\Support\Collection;
use App\Models\Warehouse\AccrualRate;

class ExportLiabilityReport extends AbstractExportService
{
    private $liabilities;

    private $currentRow = 1;

    public function __construct(Collection $liabilities)
    {
        $this->liabilities = $liabilities;

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
        foreach ($this->liabilities as $liability) {
            $this->setCellValue('A'.$row, optional($liability->user)->name);
            $this->setCellValue('B'.$row, optional($liability->user)->pobox_number);
            $this->setCellValue('C'.$row, number_format($liability->balance,2).' ');
            $this->setCellValue('D'.$row,  $liability->created_at->format('m/d/Y'));
            $row++;
        }

        $this->currentRow = $row;
    }

    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'User');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'WHR#');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'Balance');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'Date');

        $this->setBackgroundColor('A1:D1', '2b5cab');
        $this->setColor('A1:D1', 'FFFFFF');

        $this->currentRow++;
    }
}
