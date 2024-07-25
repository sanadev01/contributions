<?php

namespace App\Services\Excel\Export; 

class BulkContainerExport extends AbstractExportService
{
    private $containers;
    private $currentRow = 1;

    public function __construct($containers)
    {
        $this->containers = $containers;
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
        foreach ($this->containers as $container) {  
            if($container->hasAnjunChinaService()){
                foreach($container->orders as $order){
                    $this->setCellValue('A'.$row, $container->getUnitCode()); 
                    $this->setCellValue('B'.$row, $order->corrios_tracking_code); 
                    $this->setCellValue('C'.$row, $container->flight_number);
                    $this->setCellValue('D'.$row, $container->origin_airport??"MIA");
                    $this->setCellValue('E'.$row, $container->destination_operator_name??"GRU"); 
                    $row++;
                }  
            }
        }
    }

    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 35);
        $this->setCellValue('A1', 'CN35');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'CN23');

        $this->setColumnWidth('C', 30);
        $this->setCellValue('C1', 'waybill number');

        $this->setColumnWidth('D', 40);
        $this->setCellValue('D1', 'Origin Airport');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'Des Airport');

        $this->setBackgroundColor('A1:E1', '2b5cab');
        $this->setColor('A1:E1', 'FFFFFF');

        $this->currentRow++;
    } 
}
