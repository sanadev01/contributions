<?php

namespace App\Services\Excel\Export;
use Illuminate\Support\Collection;
use App\Models\Order;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class OrderExport extends AbstractExportService
{
    private $orders;

    private $currentRow = 1;

    public function __construct(Collection $orders)
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
        $this->setExcelHeaderRow();

        $row = $this->currentRow;

        foreach ($this->orders as $order) {
            $user = $order->user;
        
            $this->setCellValue('A'.$row, $order->warehouse_number);
            $this->setCellValue('B'.$row, $user->name);
            $this->setCellValue('C'.$row, $order->merchant);
            $this->setCellValue('D'.$row, $order->tracking_id);
            $this->setCellValue('E'.$row, $order->customer_reference);
            $this->setCellValue('F'.$row, $order->corrios_tracking_code);
            $this->setCellValue('G'.$row, $order->gross_total);
            $this->setCellValue('H'.$row, $order->getWeight('kg'));
            $this->setCellValue('I'.$row, $order->getWeight('lbs'));
            if($order->status == Order::STATUS_ORDER){
                $this->setCellValue('J'.$row, 'ORDER');
            }
            if($order->status == Order::STATUS_PAYMENT_PENDING){
                $this->setCellValue('J'.$row, 'PAYMENT_PENDING');
            }
            if($order->status == Order::STATUS_PAYMENT_DONE){
                $this->setCellValue('J'.$row, 'PAYMENT_DONE');
            }
            if($order->status == Order::STATUS_SHIPPED){
                $this->setCellValue('J'.$row, 'SHIPPED');
            }
            
            $this->setCellValue('K'.$row, $order->order_date);
            
            $row++;
        }

        $this->currentRow = $row;

        $this->setCellValue('H'.$row, "=SUM(H1:H{$row})");
        $this->setCellValue('I'.$row, "=SUM(I1:I{$row})");
        $this->mergeCells("A{$row}:G{$row}");
        $this->setBackgroundColor("A".$row, 'adfb84');
        $this->setAlignment('A'.$row, Alignment::VERTICAL_CENTER);
        $this->setCellValue('A'.$row, 'Total Order: '.$this->orders->count());



    }


    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'Order ID#');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'Name');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'Loja/Cliente');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'Carrier Tracking');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'Referência do Cliente');

        $this->setColumnWidth('F', 20);
        $this->setCellValue('F1', '	Tracking Code');

        $this->setColumnWidth('G', 20);
        $this->setCellValue('G1', 'Amount');

        $this->setColumnWidth('H', 20);
        $this->setCellValue('H1', 'Kg');
        
        $this->setColumnWidth('I', 20);
        $this->setCellValue('I1', 'Lbs');
        
        $this->setColumnWidth('J', 20);
        $this->setCellValue('J1', 'Status');

        $this->setColumnWidth('K', 20);
        $this->setCellValue('K1', 'Date');

        $this->setBackgroundColor('A1:K1', '2b5cab');
        $this->setColor('A1:K1', 'FFFFFF');

        $this->currentRow++;
    }
}
