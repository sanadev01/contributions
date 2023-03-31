<?php

namespace App\Services\Excel\Export;
class KPIReport extends AbstractExportService
{
    private $trackings;

    private $currentRow = 1;
    private $isAwaiting = null;
    private $trackingCodeUser;
    private $orderDates;

    public function __construct($trackings,$trackingCodeUser, $orderDates, $isAwaiting)
    {
        $this->isAwaiting = $isAwaiting;
        $this->trackings = $trackings;
        $this->trackingCodeUser = $trackingCodeUser;
        $this->orderDates = $orderDates;
        parent::__construct();
    }

    public function handle()
    {
        $this->prepareExcelSheet();

        return $this->download();
    }

    private function prepareExcelSheet()
    {
        $taxed = 0;
        $delivered = 0;
        $returned = 0;

        $this->setExcelHeaderRow();
        $row = $this->currentRow;
        foreach ($this->trackings as $data) {
            
            if(isset($data['evento'])) {
                if( $this->isAwaiting && optional(optional(optional($data)['evento'])[0])['descricao'] == $this->isAwaiting || !$this->isAwaiting){
                    

                    if(optional($data) && isset(optional($data)['numero'])) {
                        $poboxName = $this->trackingCodeUser[optional($data)['numero']];
                        $date = $this->orderDates[optional($data)['numero']];
                        if($poboxName)
                        $this->setCellValue('A'.$row, $poboxName);
                        
                        $this->setCellValue('B'.$row, optional($data)['numero']);
                        $this->setCellValue('C'.$row, optional($data)['categoria']);
                        $this->setCellValue('D'.$row, optional(optional(optional($data)['evento'])[count($data['evento'])-1])['data']);
                        $this->setCellValue('E'.$row, optional(optional(optional($data)['evento'])[0])['data']);
                        $this->setCellValue('F'.$row, sortTrackingEvents($data, null)['diffDates']);
                        $this->setCellValue('G'.$row, optional(optional(optional($data)['evento'])[0])['descricao']);
                        $this->setCellValue('H'.$row, sortTrackingEvents($data, null)['taxed']);
                        $this->setCellValue('I'.$row, sortTrackingEvents($data, null)['delivered']);
                        $this->setCellValue('J'.$row, sortTrackingEvents($data, null)['returned']);
                        $this->setCellValue('K'.$row, $date);
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
                    }
                }
            }
        }
            if($row>2){
                    $this->setCellValue('D'.$row, "Total");
                    $this->setCellValue('E'.$row, $row-2);
                    $this->setCellValue('H'.$row, number_format($taxed/($row-2) * 100, 2).'%');
                    $this->setCellValue('I'.$row, number_format($delivered/($row-2) * 100,2).'%');
                    $this->setCellValue('J'.$row, number_format($returned/($row-2) * 100,2).'%');
            }



        $this->currentRow = $row;
        $this->setBackgroundColor("A{$row}:K{$row}", 'adfb84');
    }

    private function setExcelHeaderRow()
    {        
        $this->setColumnWidth('A', 30);
        $this->setCellValue('A1', 'User');

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

        $this->setColumnWidth('K', 20);
        $this->setCellValue('K1', 'Order Date');

        $this->setBackgroundColor('A1:K1', '2b5cab');
        $this->setColor('A1:K1', 'FFFFFF');

        $this->currentRow++;

    }

}
