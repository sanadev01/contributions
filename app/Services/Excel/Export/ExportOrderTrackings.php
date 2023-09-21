<?php

namespace App\Services\Excel\Export;

use Illuminate\Support\Collection;

class ExportOrderTrackings extends AbstractExportService
{
    private $orders;

    private $currentRow = 1;

    public function __construct(Collection $orders)
    {
        $this->orders = $orders;

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

        foreach ($this->orders as $order) {
            $user = $order->user;

            $this->setCellValue('A'.$row, $user->pobox_number);
            $this->setCellValue('B'.$row, $user->name);
            $this->setCellValue('C'.$row, $user->email);
            $this->setCellValue('D'.$row, $order->warehouse_number);
            $this->setCellValue('E'.$row, $order->corrios_tracking_code);
            $this->setCellValue('F'.$row, $order->order_date);
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
        $this->setCellValue('D1', 'Whr#');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'Tracking Code');

        $this->setColumnWidth('F', 20);
        $this->setCellValue('F1', 'Date');

        $this->setBackgroundColor('A1:F1', '2b5cab');
        $this->setColor('A1:F1', 'FFFFFF');

        $this->currentRow++;
    }
}
