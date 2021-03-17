<?php

namespace App\Services\Excel\Export;

use Illuminate\Support\Collection;

class ExportDepositReport extends AbstractExportService
{
    private $deposits;

    private $currentRow = 1;

    public function __construct(Collection $deposits)
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

        $row = $this->currentRow;

        foreach ($this->deposits as $deposit) {

            $this->setCellValue('A'.$row, $deposit->uuid);
            $this->setCellValue('B'.$row, optional($deposit->firstOrder())->warehouse_number);
            $this->setCellValue('C'.$row, optional(optional($deposit->firstOrder())->recipient)->fullName());
            $this->setCellValue('D'.$row, optional($deposit->firstOrder())->customer_reference);
            $this->setCellValue('E'.$row, optional($deposit->firstOrder())->corrios_tracking_code);
            $this->setCellValue('F'.$row, $deposit->created_at->format('m/d/Y'));
            $this->setCellValue('G'.$row, $deposit->amount);
            $this->setCellValue('H'.$row, $deposit->isCredit() ? 'Credit' : 'Debit');
            $row++;
        }

        $this->currentRow = $row;
    }

    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'DP#');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'WHR#');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'Recipient');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'Customer Reference#');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'Tracking Code');

        $this->setColumnWidth('F', 20);
        $this->setCellValue('F1', 'Date');

        $this->setColumnWidth('G', 20);
        $this->setCellValue('G1', 'Amount');

        $this->setColumnWidth('H', 20);
        $this->setCellValue('H1', 'Credit/Debit');

        $this->setBackgroundColor('A1:F1', '2b5cab');
        $this->setColor('A1:H1', 'FFFFFF');

        $this->currentRow++;
    }
}
