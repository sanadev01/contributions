<?php

namespace App\Services\Excel\Export;
use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AccuralRateExport extends AbstractExportService
{
    private $rates;
    private $countryId;

    private $currentRow = 1;

    public function __construct(Collection $rates)
    {
        $this->rates = $rates;
        $this->countryId = optional(optional($this->rates)[0])->country_id;
        parent::__construct();
    }

    public function handle()
    {
        $this->prepareExcelSheet();

        return $this->download();
    }

    private function prepareExcelSheet()
    {
        $this->setSecondHeaderRow();

        $row = $this->currentRow;

        foreach ($this->rates as $rate) {
            $this->setCellValue('A'.$row, $rate->getServiceName());
            $this->setCellValue('B'.$row, $rate->weight);
            $this->setCellValue('C'.$row,  $rate->cwb);
            $this->setCellValue('D'.$row,  $rate->gru);
            $row++;
        }

        $this->currentRow = $row;

    }

    private function setSecondHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'Service Name');
        if($this->countryId == 30){
            $this->setColumnWidth('B', 20);
            $this->setCellValue('B1', 'Weight');

            $this->setColumnWidth('C', 20);
            $this->setCellValue('C1', 'CWB');

            $this->setColumnWidth('D', 20);
            $this->setCellValue('D1', 'GRU');
        }else{
            $this->setColumnWidth('B', 20);
            $this->setCellValue('B1', 'Weight');

            $this->setColumnWidth('C', 20);
            $this->setCellValue('C1', 'SCL (SRM)');

            $this->setColumnWidth('D', 20);
            $this->setCellValue('D1', 'SCL (SRP)');
        }
        $this->setBackgroundColor('A1:D1', '2b5cab');
        $this->currentRow++;
        return true;
    }
}
