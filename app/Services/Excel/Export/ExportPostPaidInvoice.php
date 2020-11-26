<?php

namespace App\Services\Excel\Export;

use App\Models\PaymentInvoice;
use Illuminate\Support\Collection;

class ExportPostPaidInvoice extends AbstractExportService
{
    private $paymentInvoice;

    private $currentRow = 1;

    public function __construct(PaymentInvoice $paymentInvoice)
    {
        $this->paymentInvoice = $paymentInvoice;

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

        foreach ($this->paymentInvoice->orders as $order) {
            $this->setCellValue('A'.$row, '');
            $this->setCellValue('B'.$row, $order->recipient->first_name);
            $this->setCellValue('C'.$row, $order->corrios_tracking_code);
            $this->setCellValue('D'.$row, $order->warehouse_number);
            $this->setCellValue('E'.$row, $order->customer_reference);
            $this->setCellValue('F'.$row, round($order->weight,2));
            $this->setCellValue('G'.$row, $order->shipping_value);
            $row++;
        }

        $this->currentRow = $row;
    }

    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'Client');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'Recipient');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'Tracking#');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'Warehouse#');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'Leve Order#');

        $this->setColumnWidth('F', 20);
        $this->setCellValue('F1', 'Weight');

        $this->setColumnWidth('G', 20);
        $this->setCellValue('G1', 'Amount');

        $this->setBackgroundColor('A1:F1', '2b5cab');
        $this->setColor('A1:F1', 'FFFFFF');

        $this->currentRow++;
    }
}
