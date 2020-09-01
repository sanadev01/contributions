<?php

namespace App\Services\Excel\Export;

use Illuminate\Support\Collection;

class ExportOrderTrackings extends AbstractExportService
{
    private $trackings;

    private $currentRow = 1;

    public function __construct(Collection $trackings)
    {
        $this->trackings = $trackings;

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

        foreach ($this->trackings as $tracking) {
            $order = $tracking->order;
            $user = $order->user;
            $shipment = $order->shipment;

            $this->setCellValue('A'.$row, $user->pobox_number);
            $this->setCellValue('B'.$row, $user->name);
            $this->setCellValue('C'.$row, $user->email);
            $this->setCellValue('D'.$row, $shipment->whr_number);
            $this->setCellValue('E'.$row, $tracking->tracking_number);
            $this->setCellValue('F'.$row, $tracking->link);
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
        $this->setCellValue('E1', 'Tracking ID');

        $this->setColumnWidth('F', 20);
        $this->setCellValue('F1', 'Link');

        $this->setBackgroundColor('A1:F1', '2b5cab');
        $this->setColor('A1:F1', 'FFFFFF');

        $this->currentRow++;
    }
}
