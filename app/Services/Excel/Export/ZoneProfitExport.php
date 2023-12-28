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
            $this->setCellValue('A'.$row, $rate->shippingService->name);
            $this->setCellValue('B'.$row, $rate->country->name);
            $this->setCellValue('C'.$row, $rate->profit_percentage);
            $row++;
        }

        $this->currentRow = $row;

    }

    private function setFirstHeaderRow()
    {
        $group = 'Group ' . $this->rates->first()->group_id;
        $this->setColumnWidth('A', 20);
        $this->mergeCells("A1:C1");
        $this->setAlignment('A1', 'center');
        $this->setCellValue('A1', $group);
                
        $this->setBackgroundColor('A1:C1', '2b5cab');
        $this->setColor('A1:C1', 'FFFFFF');
        $this->currentRow++;
        return true;
    }

    private function setSecondHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A2', 'Shipping Service');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B2', 'Country');
        
        $this->setColumnWidth('C', 20);
        $this->setCellValue('C2', 'Profit Percentage');
        
        $this->setBackgroundColor('A2:C2', '2b5cab');
        $this->setColor('A2:C2', 'FFFFFF');
        $this->currentRow++;
        return true;
    }

}
