<?php

namespace App\Services\Excel\Export;

use App\Models\Order;

class UnPaidOrdersReport extends AbstractExportService
{
    private $trackings;

    private $currentRow = 1;

    public function __construct($trackings)
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
        
        foreach ($this->trackings as $order) {
            $this->setCellValue('A'.$row, $order['user']['name'] );
            $this->setCellValue('B'.$row, $order['user']['pobox_number']);
            $this->setCellValue('C'.$row, $order['warehouse_number']);
            $this->setCellValue('D'.$row, $order['corrios_tracking_code']);
            $this->setCellValue('E'.$row, date('d-M-Y', strtotime($order['order_date'])));
            $row++;
        }

        $this->currentRow = $row;
        $this->setBackgroundColor("A{$row}:D{$row}", 'adfb84');
    }

    private function setExcelHeaderRow()
    {        
        $this->setColumnWidth('A', 30);
        $this->setCellValue('A1', 'User Name');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'PoBox Number');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'Warehouse #');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'Tracking Code');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'Create Date');

        $this->setBackgroundColor('A1:E1', '2b5cab');
        $this->setColor('A1:E1', 'FFFFFF');

        $this->currentRow++;

    }

}
