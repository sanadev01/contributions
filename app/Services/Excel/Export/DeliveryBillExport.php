<?php

namespace App\Services\Excel\Export;

use Illuminate\Support\Collection;

class DeliveryBillExport extends AbstractExportService
{
    private $deliveryBills;

    private $currentRow = 1;

    public function __construct(Collection $deliveryBills)
    {
        $this->deliveryBills = $deliveryBills;

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

        foreach ($this->deliveryBills as $deliveryBill) {
            $this->setCellValue('A'.$row, $deliveryBill->name);
            $this->setCellValue('B'.$row, $deliveryBill->request_id);
            $this->setCellValue('C'.$row, $deliveryBill->cnd38_code);
            $this->setCellValue('E'.$row, $deliveryBill->created_at);
            $row++;
        }

        $this->currentRow = $row;
    }

    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'Name');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'Request ID');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'CN38 Code');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'Country Origin');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'Date');

        $this->setBackgroundColor('A1:E1', '2b5cab');
        $this->setColor('A1:E1', 'FFFFFF');

        $this->currentRow++;
    }

}
