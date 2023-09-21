<?php

namespace App\Services\Excel\Export;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\ShippingService;
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
        $this->service = optional(optional($this->rates)[0])->service;
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
            if($this->service()){
                $this->setCellValue('E'.$row,  $rate->commission);
            }
            $row++;
        }

        $this->currentRow = $row;

    }

    private function setSecondHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'Service Name');
        
        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'Weight');
        
        if($this->countryId == Order::BRAZIL){

            $this->setColumnWidth('C', 20);
            $this->setCellValue('C1', 'CWB');

            $this->setColumnWidth('D', 20);
            $this->setCellValue('D1', 'GRU');
            if($this->service()){
                $this->setColumnWidth('E', 20);
                $this->setCellValue('E1', 'Commission');
            }
        }else{
            
            $this->setColumnWidth('C', 20);
            $this->setCellValue('C1', 'SCL (SRM)');

            $this->setColumnWidth('D', 20);
            $this->setCellValue('D1', 'SCL (SRP)');
        }
        $this->setBackgroundColor('A1:E1', '2b5cab');
        $this->setColor('A1:E1', 'FFFFFF');
        $this->currentRow++;
        return true;
    }

    private function service()
    {
        if($this->service == ShippingService::AJ_Packet_Standard || $this->service == ShippingService::AJ_Packet_Express){
            return true;
        }
        return false;
    }
}
