<?php

namespace App\Services\Excel\Export;
class ExportLiabilityReport extends AbstractExportService
{ 

    private $currentRow = 1;
    private $deposits;
    public function __construct($deposits)
    { 
        $this->deposits = $deposits;
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
        foreach ($this->deposits as $deposit) {
            $this->setCellValue('A'.$this->currentRow, optional($deposit->user)->name);
            $this->setCellValue('B'.$this->currentRow, optional($deposit->user)->pobox_number);
            $this->setCellValue('C'.$this->currentRow, number_format($deposit->balance,2).' ');
            $this->setCellValue('D'.$this->currentRow, $deposit->created_at->format('m/d/Y'));  
            $this->currentRow++;
        }
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
