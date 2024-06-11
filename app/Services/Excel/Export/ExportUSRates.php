<?php

namespace App\Services\Excel\Export;

use App\Services\Excel\Export\AbstractExportService;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ExportUSRates extends AbstractExportService
{
    private $rates;
    private $order;
    private $chargableWeight;
    private $weightInOtherUnit;

    private $currentRow = 2;

    public function __construct($rates, $order, $chargableWeight, $weightInOtherUnit)
    {
        $this->rates = $rates;
        $this->order = $order;
        $this->chargableWeight = $chargableWeight;
        $this->weightInOtherUnit = $weightInOtherUnit;

        parent::__construct();
    }

    public function handle()
    {
        $this->prepareExcelSheet();

        return $this->download();
    }

    private function prepareExcelSheet()
    {
        $this->setExcelHeader();

        $row = $this->currentRow;

        foreach ($this->rates as $rate) {
            $this->setCellFormat('A'.$row, '#');
            $this->setAlignment('A'.$row, 'left');
            $this->setCellValue('A'.$row, $rate['name']);

            $this->setCellFormat('B'.$row, '#');
            $this->setAlignment('B'.$row, 'left');
            $this->setCellValue('B'.$row, $this->setWeight());

            $this->setCellFormat('C'.$row, '#');
            $this->setAlignment('C'.$row, 'right');
            $this->setCellValue('C'.$row, $rate['rate'].' USD');

            $row++;
        }

        $this->currentRow = $row;
    }

    private function setExcelHeader()
    {
        $this->mergeCells('A1:C1');
        $this->setColor('A1:C1', 'FFFFFF');
        $this->setBackgroundColor('A1:C1', '0000FF');
        $this->setRowHeight('1', 15);
        $this->setBold('A1', 700);
        $this->setAlignment('A1:C1', 'center');
        $this->setCellValue('A1', 'US Services Rates');

        $this->setColumnWidth('A', 20);
        $this->setCellValue('A2', 'Service Name');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B2', 'Weight');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C2', 'Cost');

        $this->setBackgroundColor('A2:C2', '44546A');
        $this->setColor('A2:C2', 'FFFFFF');

        $this->currentRow++;
    }

    private function setWeight()
    {
        if ($this->order['measurement_unit'] == 'kg/cm') {
            return $this->chargableWeight.' kg  ('.$this->weightInOtherUnit.' lbs)';
        }else {
            return $this->chargableWeight.' lbs  ('.$this->weightInOtherUnit.' kg)';
        }
    }
}
