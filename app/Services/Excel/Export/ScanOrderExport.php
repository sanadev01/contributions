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
            
            $this->setCellValue('A'.$row, $this->count);
            $this->setColor('A'.$row, 'FF0D0D');
            $this->setAlignment('A'.$row, Alignment::HORIZONTAL_LEFT);
            $this->setCellValue('B'.$row, $order->corrios_tracking_code);
            $this->setCellValue('C'.$row,  $order->corrios_tracking_code);
            
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
        $this->setCellValue('A'.$row, '#');
        $this->setBold('A'.$row, true);
        $this->setColor('A'.$row, 'FF0D0D');

        $this->setCellValue('B'.$row, 'Tracking #');
        $this->setBold('B'.$row, true);

        $this->setCellValue('D'.$row, 'Notes');
        $this->setBold('D'.$row, true);

        $this->currentRow++;

        return true;
    }
}
