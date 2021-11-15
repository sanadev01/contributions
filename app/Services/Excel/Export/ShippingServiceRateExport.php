<?php

namespace App\Services\Excel\Export;

class ShippingServiceRateExport extends AbstractExportService
{
    private $rates;

    private $currentRow = 2;

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

        $row = $this->currentRow;
        foreach ($this->rates as $rate) {
            
            $this->setCellValue('A'.$row, $rate['weight'] );
            $this->setCellValue('B'.$row, ($rate['weight']/1000) . ' Kg' );
            $this->setCellValue('C'.$row, $rate['leve'] );
            $row++;
        }
        $this->currentRow = $row;
    }

    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A2', 'Weight Grams');
        
            $this->setColumnWidth('B', 20);
            $this->setCellValue('B2', 'Weight Kg');
            
            $this->setColumnWidth('C', 20);
           (\Request::route()->getName() == 'admin.rates.download-shipping-rates') ? $this->setCellValue('C2', 'Rate ($)') : $this->setCellValue('C2', 'Leve');
            
        $this->currentRow++;
    }
}
