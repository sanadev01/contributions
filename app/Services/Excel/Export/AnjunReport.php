<?php

namespace App\Services\Excel\Export;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Repositories\Reports\AnjunReportsRepository;

class AnjunReport extends AbstractExportService
{
    private $orders;
    private $request;

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
            $this->setCellValue('A'.$row, $order->order_date);
            $this->setCellValue('B'.$row, $order->warehouse_number);
            $this->setCellValue('C'.$row, $order->user->name);
            $this->setCellValue('D'.$row, $order->corrios_tracking_code);
            $this->setCellValue('E'.$row, round($order->shipping_value,2));
            $this->setCellValue('F'.$row, round($order->total,2));
            $this->setCellValue('G'.$row, round($order->comission,2));
            $row++;
        }

        $this->currentRow = $row;
    }

    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'Date');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'Warehouse No.');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'User Name');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'Tracking Code');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'Carrier Cost USD');

        $this->setColumnWidth('F', 20);
        $this->setCellValue('F1', 'Amount USD');

        $this->setColumnWidth('G', 20);
        $this->setCellValue('G1', 'Anjun Amount USD');

        $this->setBackgroundColor('A1:G1', '2b5cab');
        $this->setColor('A1:G1', 'FFFFFF');

        $this->currentRow++;

    }
}
