<?php

namespace App\Services\Excel\Export;
use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ScanOrderExport extends AbstractExportService
{
    private $orders;

    private $pieces;

    private $totalWeight;

    private $currentRow = 1;

    private $count = 1;

    public function __construct(Collection $orders)
    {
        $this->orders = $orders;
        $this->pieces = $this->orders->count();
        $this->totalWeight = $this->orders->sum('weight');

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
            $this->setCellValue('A'.$row, $order->corrios_tracking_code);
            $this->setCellValue('B'.$row, $order->user->pobox_number);
            $this->setCellValue('C'.$row, $order->merchant);
            $this->setCellValue('D'.$row, $order->length . ' x ' . $order->length . ' x ' . $order->height );
            $this->setCellValue('E'.$row, $order->getWeight('kg'));
            $this->setCellValue('F'.$row, $order->id);
            $this->setCellValue('G'.$row, $order->recipient->first_name);
            $this->setCellValue('H'.$row, $order->order_date->format('m-d-Y'));
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
        $this->setCellValue('B1', 'Homedeliverybr');
        $this->setCellValue('B2', '2200 NW 129th Avenue');
        $this->setCellValue('B3', 'Suite#100');
        $this->setCellValue('B4', 'Miami, FL 33182');

        $this->setCellValue('B6', 'Recieved on:');
        $this->setBold('B6', true);
        $this->setColor('B6', 'FF0D0D');

        $this->setColumnWidth('C', 25);
        $this->setCellValue('C6', Carbon::now()->toFormattedDateString());
        $this->setBold('C6', true);
        $this->setColor('C6', '0A0000');

        $this->setColumnWidth('D', 25);
        $this->setCellValue('D1', 'Pieces : '.$this->pieces);
        $this->setCellValue('D2', 'Total Weight : '.$this->totalWeight.' Kg');
        
        $this->setCellValue('D6', Carbon::now()->format('d/m/Y H:i:s'));
        
        $this->setBackgroundColor('A6:D6', 'C4C2C2');

        $this->currentRow = 7;
    }

    private function setSecondHeaderRow($row)
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A7', 'Tracking Code');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B7', 'POBOX#');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C7', 'Client');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D7', 'Dimensions');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E7', 'Weight Kg');

        $this->setColumnWidth('F', 20);
        $this->setCellValue('F7', 'Reference#');

        $this->setColumnWidth('G', 20);
        $this->setCellValue('G7', 'Recpient');

        $this->setColumnWidth('H', 20);
        $this->setCellValue('H7', 'Order Date');

        $this->setBackgroundColor('A7:H7', '2b5cab');
        $this->setColor('A7:H7', 'FFFFFF');
        $this->currentRow++;

        return true;
    }
}
