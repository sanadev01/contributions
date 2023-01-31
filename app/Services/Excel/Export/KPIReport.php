<?php

namespace App\Services\Excel\Export;

use DateTime;
use App\Models\Order;
use Exception;
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
        $total = 0;
        $taxed = 0;
        $delivered = 0;
        $returned = 0;

        $this->setExcelHeaderRow();
        $row = $this->currentRow;
        foreach ($this->trackings as $data) {
            if(isset($data['evento'])) {
                if(optional($data) && isset(optional($data)['numero'])) {
                     $userName = '';
                     try{
                     if(optional($data)['numero']){
                     $user = Order::where('corrios_tracking_code', $data['numero'])->first()->user;
                     $userName = $user->getFullName() . $user->pobox_number;
                    }
                     }catch(Exception $e){}

                    $this->setCellValue('A'.$row, $userName);
                    $this->setCellValue('B'.$row, optional($data)['numero']);
                    $this->setCellValue('C'.$row, optional($data)['categoria']);
                    $this->setCellValue('D'.$row, optional(optional(optional($data)['evento'])[count($data['evento'])-1])['data']);
                    $this->setCellValue('E'.$row, optional(optional(optional($data)['evento'])[0])['data']);
                    $this->setCellValue('F'.$row, sortTrackingEvents($data, null)['diffDates']);
                    $this->setCellValue('G'.$row, optional(optional(optional($data)['evento'])[0])['descricao']);
                    $this->setCellValue('H'.$row, sortTrackingEvents($data, null)['taxed']);
                    $this->setCellValue('I'.$row, sortTrackingEvents($data, null)['delivered']);
                    $this->setCellValue('J'.$row, sortTrackingEvents($data, null)['returned']);
                    $row++;
                    if(sortTrackingEvents($data, null)['taxed']=='Yes'){
                        $taxed++;
                    }
                    if(sortTrackingEvents($data, null)['delivered']=='Yes'){
                        $delivered++;
                    }
                    if(sortTrackingEvents($data, null)['returned']=='Yes'){
                        $returned++;
                    }
                    $total++;
                }
            }
        }
            if($total){
                    $this->setCellValue('E'.$row, "Total");
                    $this->setCellValue('F'.$row, $total);
                    $this->setCellValue('H'.$row, number_format($taxed/$total * 100, 2).'%');
                    $this->setCellValue('I'.$row, number_format($delivered/$total * 100,2).'%');
                    $this->setCellValue('J'.$row, number_format($returned/$total * 100,2).'%');
            }


        $this->currentRow = $row;
        $this->setBackgroundColor("A{$row}:I{$row}", 'adfb84');
    }

    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'User Name');
        
        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'Tracking');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'Type Package');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'First Event');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', '	Last Event');

        $this->setColumnWidth('F', 20);
        $this->setCellValue('F1', 'Days Between');

        $this->setColumnWidth('G', 20);
        $this->setCellValue('G1', 'Last Event');

        $this->setColumnWidth('H', 20);
        $this->setCellValue('H1', 'Taxed');

        $this->setColumnWidth('I', 20);
        $this->setCellValue('I1', 'Delivered');

        $this->setColumnWidth('J', 20);
        $this->setCellValue('J1', 'Returned');

        $this->setBackgroundColor('A1:J1', '2b5cab');
        $this->setColor('A1:J1', 'FFFFFF');

        $this->currentRow++;

    }

}
