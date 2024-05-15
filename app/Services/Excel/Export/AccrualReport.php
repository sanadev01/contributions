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
        $grossTotalPaid=0;
        $totalPaidTax=0; 

        $this->setExcelHeaderRow();
        $row = $this->currentRow; 
        foreach ($this->orders as $order) {
                   $this->setCellValue('A'.$row, $order->user->name);
                   $this->setCellValue('B'.$row, $order->warehouse_number);
                   $this->setCellValue('C'.$row, $order->carrier); 
                   $this->setCellValue('D'.$row, ''.$order->gross_total); 
                   $this->setCellValue('E'.$row, ''.(string) $order->tax_and_duty);  
                   $this->setCellValue('F'.$row, $order->order_date->format('m-d-Y'));
                   if($order->isPaid()){
                        $grossTotalPaid += $order->gross_total; 
                        $totalPaidTax   += $order->tax_and_duty;
                        $this->setCellValue('G'.$row, "Paid");
                        $this->setBackgroundColor("A{$row}:G{$row}", 'adfb84');
                    }else{
                        $this->setCellValue('G'.$row, "Un-paid"); 
                    }
                    $row++;
        }
        if($row>2){
                $this->setCellValue('C'.$row, "Total orders");
                $this->setCellValue('D'.$row, number_format($this->orders->sum('gross_total'),2)); 
                $this->setCellValue('E'.$row, number_format($this->orders->sum('tax_and_duty'),2)); 
                $this->currentRow = $row;
                $this->setBackgroundColor("A{$row}:G{$row}", 'fcf7b6');
                $row++;
                $this->setCellValue('C'.$row, "Total Paid");
                $this->setCellValue('D'.$row, number_format($grossTotalPaid,2)); 
                $this->setCellValue('E'.$row, number_format($totalPaidTax,2)); 
                $this->currentRow = $row;
                $this->setBackgroundColor("A{$row}:G{$row}", 'adfb84');
        }
 


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
        $this->setColumnWidth('G', 20);
        $this->setCellValue('G1', 'Payment');

        $this->setBackgroundColor('A1:G1', '2b5cab');
        $this->setColor('A1:G1', 'FFFFFF');

        $this->currentRow++;

    }

}
