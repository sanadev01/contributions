<?php

namespace App\Services\Excel\Export;
use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ScanOrderExport extends AbstractExportService
{
    private $orders;

    private $currentRow = 1;

    private $count = 1;

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

        $this->setSecondHeaderRow($row);

        $row = $this->currentRow;

        foreach ($this->orders as $order) {
            
            $this->setCellValue('A'.$row, $this->count);
            $this->setColor('A'.$row, 'FF0D0D');
            $this->setAlignment('A'.$row, Alignment::HORIZONTAL_LEFT);
            $this->setCellValue('B'.$row, $order->corrios_tracking_code);
            $this->setCellValue('C'.$row,  date('d-m-Y', strtotime($order->order_date)));
            $this->setCellValue('D'.$row,  optional($order->user)->pobox_number);
            
            $this->count++ ;
            $row++;
        }

        $this->currentRow = $row;

    }


    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', '');

        $this->setColumnWidth('B', 25);
        $this->setCellValue('B1', 'Recieved on:');
        $this->setBold('B1', true);
        $this->setColor('B1', 'FF0D0D');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', Carbon::now()->format('d/m/Y H:i:s'));
        $this->setBold('C1', true);
        $this->setColor('C1', '0A0000');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'POBOX Number');
        
        $this->setBackgroundColor('A1:D1', 'C4C2C2');

        $this->currentRow++;
    }

    private function setSecondHeaderRow($row)
    {
        $this->setCellValue('A'.$row, '#');
        $this->setBold('A'.$row, true);
        $this->setColor('A'.$row, 'FF0D0D');

        $this->setCellValue('B'.$row, 'Tracking #');
        $this->setBold('B'.$row, true);
        
        $this->setCellValue('C'.$row, 'Order Date');
        $this->setBold('C'.$row, true);

        $this->currentRow++;

        return true;
    }
}
