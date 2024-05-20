<?php

namespace App\Services\Excel\Export; 
class OrderUpdateExport extends AbstractExportService
{
    private $orders; 

    private $currentRow = 1;

    public function __construct($orders)
    {
        $this->orders = $orders; 

        parent::__construct();
    }

    public function handle()
    {
        $this->prepareExcelSheet();

        return $this->downloadExcel();
    }

    private function prepareExcelSheet()
    {
        $this->setExcelHeaderRow();

        $row = $this->currentRow;


        foreach ($this->orders as $order) {
            $this->setCellValue('A'.$row, $order['tracking_old']);
            $this->setCellValue('B'.$row, $order['warehouse'] );
            $this->setCellValue('C'.$row, $order['tracking_new']);
            $this->setCellValue('D'.$row, $order['poboxName']); 
            $this->setCellValue('E'.$row, $order['link']); 
            $row++;
        }


    }

    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'Tracking old');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'WareHouse No ');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'New tracking');
        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'pobox Name');
        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'Link');

     
        $this->currentRow++;
    }

}