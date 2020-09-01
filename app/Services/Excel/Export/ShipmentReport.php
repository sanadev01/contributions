<?php

namespace App\Services\Excel\Export;

use Illuminate\Support\Collection;

class ShipmentReport extends AbstractExportService
{
    private $users;

    private $currentRow = 1;

    public function __construct(Collection $users)
    {
        $this->users = $users;

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

        foreach ($this->users as $user) {
            $this->setCellValue('A'.$row, $user->pobox_number);
            $this->setCellValue('B'.$row, $user->name);
            $this->setCellValue('C'.$row, $user->email);
            $this->setCellValue('D'.$row, $user->orders_count);
            $row++;
        }

        $this->currentRow = $row;
    }

    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'POBOX#');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'Name');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'Email');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', '# of Shipments');

        $this->setBackgroundColor('A1:D1', '2b5cab');
        $this->setColor('A1:D1', 'FFFFFF');

        $this->currentRow++;
    }
}
