<?php

namespace App\Services\Excel\Export;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ProfitPackageRateExport extends AbstractExportService
{
    private $rates;

    private $currentRow = 1;

    public function __construct(Collection $rates)
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
            
            $this->setCellValue('A'.$row, optional($rate)['weight'] . ' g');

            if(Auth::user()->isUser()){
                $this->setCellValue('B'.$row, round(optional(optional($rate)['shipping'])[0]*(optional($rate)['profit']/100)+optional(optional($rate)['shipping'])[0],2) );
            }
            if(Auth::user()->isAdmin()){
                $this->setCellValue('B'.$row, optional(optional($rate)['shipping'])[0] );
                $this->setCellValue('C'.$row, optional($rate)['profit'] );
                $this->setCellValue('D'.$row, "=ROUND(B$row*(C$row/100)+B$row,2)");
            }
            $row++;
        }
        $this->currentRow = $row;
    }

    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'weight');
        if(Auth::user()->isUser()){

            $this->setColumnWidth('B', 20);
            $this->setCellValue('B1', 'selling rate');

            $this->setBackgroundColor('A1:B1', '2b5cab');
            $this->setColor('A1:B1', 'FFFFFF');
        }
        
        if(Auth::user()->isAdmin()){
            $this->setColumnWidth('B', 20);
            $this->setCellValue('B1', 'cost');
            
            $this->setColumnWidth('C', 20);
            $this->setCellValue('C1', 'profit percentage');
            
            $this->setColumnWidth('D', 20);
            $this->setCellValue('D1', 'selling rate');

            $this->setBackgroundColor('A1:D1', '2b5cab');
            $this->setColor('A1:D1', 'FFFFFF');
        }

        $this->currentRow++;
    }
}
