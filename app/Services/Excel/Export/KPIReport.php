<?php

namespace App\Services\Excel\Export;

use DateTime;
use App\Models\Order;
use Illuminate\Support\Collection;

class KPIReport extends AbstractExportService
{
    private $trackings;
    private $request;

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
        foreach ($this->trackings['return']['objeto'] as $data) {

            if(optional($data) && isset($data['evento'])) {
                $this->setCellValue('A'.$row, $data['numero']);
                $this->setCellValue('B'.$row, $data['categoria']);
                $this->setCellValue('C'.$row, $data['evento'][count($data['evento'])-1]['data']);
                $this->setCellValue('D'.$row, $data['evento'][0]['data']);
                $this->setCellValue('E'.$row, sortTrackingEvents($data)['diffDates']);
                $this->setCellValue('F'.$row, $data['evento'][0]['descricao']);
                $this->setCellValue('G'.$row, sortTrackingEvents($data)['taxed']);
                $this->setCellValue('H'.$row, sortTrackingEvents($data)['delivered']);
                $this->setCellValue('I'.$row, sortTrackingEvents($data)['returned']);
                $row++;
            }
        }

        $this->currentRow = $row;
        $this->setBackgroundColor("A{$row}:I{$row}", 'adfb84');
    }

    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'Tracking');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'Type Package');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'First Event');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', '	Last Event');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'Days Between');

        $this->setColumnWidth('F', 20);
        $this->setCellValue('F1', 'Last Event');

        $this->setColumnWidth('G', 20);
        $this->setCellValue('G1', 'Taxed');

        $this->setColumnWidth('H', 20);
        $this->setCellValue('H1', 'Delivered');

        $this->setColumnWidth('I', 20);
        $this->setCellValue('I1', 'Returned');

        $this->setBackgroundColor('A1:I1', '2b5cab');
        $this->setColor('A1:I1', 'FFFFFF');

        $this->currentRow++;

    }

}
