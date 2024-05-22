<?php

namespace App\Services\Excel\Export;

use App\Models\User;
use Illuminate\Support\Collection;

class ExportNameListTest extends AbstractExportService
{
    private $orders;
    private $user;

    private $currentRow = 1;

    public function __construct($orders)
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

        $row= $this->currentRow;

        foreach ($this->orders as $order) {
            $this->setCellValue('A'.$row, $order->user->pobox_number);
            $this->setCellValue('B'.$row, $order->warehouse_number); 
            $this->setCellValue('C'.$row, optional($order)->corrios_tracking_code); 
            $row++;
        } 
        $this->setBackgroundColor("A{$row}:F{$row}", 'adfb84');
    }

    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'Pobox Number');
        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'Warehouse number');
        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'Tracking Code');
        $this->setBackgroundColor('A1:C1', '2b5cab');
        $this->setColor('A1:C1', 'FFFFFF');


        $this->currentRow++;
    }
}
