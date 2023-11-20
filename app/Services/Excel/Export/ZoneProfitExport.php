<?php

namespace App\Services\Excel\Export;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\ShippingService;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ZoneProfitExport extends AbstractExportService
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
        $this->setFirstHeaderRow();
        $this->setSecondHeaderRow();

        $row = $this->currentRow;

        foreach ($this->rates as $rate) {
            $this->setCellValue('A'.$row, $rate->country->name);
            $this->setCellValue('B'.$row, $rate->profit_percentage);
            $row++;
        }

        $this->currentRow = $row;

    }

    private function setFirstHeaderRow()
    {
        $zone = 'Zone ' . $this->rates->first()->zone_id;
        $this->setColumnWidth('A', 20);
        $this->mergeCells("A1:B1");
        $this->setAlignment('A1', 'center');
        $this->setCellValue('A1', $zone);
                
        $this->setBackgroundColor('A1:B1', '2b5cab');
        $this->setColor('A1:B1', 'FFFFFF');
        $this->currentRow++;
        return true;
    }

    private function setSecondHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A2', 'Country');
        
        $this->setColumnWidth('B', 20);
        $this->setCellValue('B2', 'Profit Percentage');
        
        $this->setBackgroundColor('A2:B2', '2b5cab');
        $this->setColor('A2:B2', 'FFFFFF');
        $this->currentRow++;
        return true;
    }

}
