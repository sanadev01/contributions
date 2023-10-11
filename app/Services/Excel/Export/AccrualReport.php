<?php

namespace App\Services\Excel\Export;
class AccrualReport extends AbstractExportService
{ 
 
    private $orders;
    private $currentRow=1;
    public function __construct($orders)
    { 
    
        $this->orders = $orders;
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
        $grossTotals=0;
        // $delivered = 0;
        // $returned = 0;

        $this->setExcelHeaderRow();
        $row = $this->currentRow; 
        foreach ($this->orders as $order) {
            if($order->tax_and_duty > 0) {
                $tax = number_format($order->tax_and_duty,2);
                $grossTotal = round($order->gross_total,2);
                $taxed = $taxed + $tax;
                $grossTotals = $grossTotals + $grossTotal;
                   $this->setCellValue('A'.$row, $order->user->name);
                   $this->setCellValue('B'.$row, $order->warehouse_number);
                   $this->setCellValue('C'.$row, $order->carrier); 
                   $this->setCellValue('D'.$row, ''.$grossTotal); 
                   $this->setCellValue('E'.$row, ''.(string) $tax);  
                   $this->setCellValue('F'.$row, $order->order_date->format('m-d-Y'));
                   $row++;
            }
        }
            if($row>2){
                    $this->setCellValue('C'.$row, "Total");
                    $this->setCellValue('D'.$row, $grossTotals); 
                    $this->setCellValue('E'.$row, $taxed); 
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
        $this->setCellValue('D1', 'Amount');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'Tax & duty'); 

        $this->setColumnWidth('F', 20);
        $this->setCellValue('F1', 'Order Date');

        $this->setBackgroundColor('A1:F1', '2b5cab');
        $this->setColor('A1:F1', 'FFFFFF');

        $this->currentRow++;

    }

}
