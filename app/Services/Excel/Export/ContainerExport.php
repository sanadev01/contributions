<?php

namespace App\Services\Excel\Export;

class ContainerExport extends AbstractExportService
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
            foreach ($container->orders as $order) {
                $this->setCellValue('A' . $row, $container->dispatch_number);
                $this->setCellValue('B' . $row, $container->seal_no);
                $this->setCellValue('C' . $row, $container->service_subclass_name);
                $this->setCellValue('D' . $row, $container->unit_code);
                $this->setCellValue('E' . $row, $order->corrios_tracking_code);
                $this->setCellValue('F' . $row, optional($container->updated_at)->format('m/d/Y'));
                $row++;
            }
        }
    }

    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'Dispatch Number');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'Seal No#');

        $this->setColumnWidth('C', 30);
        $this->setCellValue('C1', 'Service Class');

        $this->setColumnWidth('D', 40);
        $this->setCellValue('D1', 'Unit Code');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'Tracking Code');

        $this->setColumnWidth('F', 20);
        $this->setCellValue('F1', 'Date');

        $this->setBackgroundColor('A1:F1', '2b5cab');
        $this->setColor('A1:F1', 'FFFFFF');

        $this->currentRow++;
    }
}
