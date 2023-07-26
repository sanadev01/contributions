<?php

namespace App\Services\Excel\Export;

class ShippingServiceRegionRateExport extends AbstractExportService
{
    private $rates;

    private $currentRow = 2;
    function numberToLetter($number)
    {
        $first = $number / 26;
        $second = $number % 26;
        if ($first >= 1) {
            $first--;
            return (range('A', 'Z')[number_format(floor($first), 0)]) . (range('A', 'Z')[number_format(floor($second), 0)]);
        } else {
            return (range('A', 'Z')[number_format(floor($second), 0)]);
        }
    }

    public function __construct($rates)
    {
        $this->rates = $rates;
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

        foreach ($this->rates as $rateKey => $rate) {

            $row = 3;

            foreach ($rate->data as $dataKey => $data) {
                if ($rateKey == 0) {
                    $this->setCellValue("A" . $row, $data['weight']);
                    $this->setCellValue("B" . $row, ($data['weight'] / 1000) . ' Kg');
                };
                $this->setCellValue($this->numberToLetter($rateKey + 2) . $row, $data['leve']);
                $row++;
            }
        }
        // $this->currentRow = $row;
    }

    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A2', 'Weight Grams');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B2', 'Weight Kg');
        foreach ($this->rates as $key => $rate) {
            $this->setColumnWidth($this->numberToLetter($key + 2), 20);
            $this->setCellValue($this->numberToLetter($key + 2) . '2', optional($rate->region)->name . ' Rate ($)');
        }
        $this->currentRow++;
    }
}
