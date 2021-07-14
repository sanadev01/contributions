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
            $this->setCellValue('H'.$row, number_format($order->dangrous_goods,2));
            $this->setCellValue('I'.$row, $order->getWeight('kg'));
            $this->setCellValue('J'.$row, $order->getWeight('lbs'));
            if($order->status == Order::STATUS_ORDER){
                $this->setCellValue('K'.$row, 'ORDER');
            }
            if($order->status == Order::STATUS_PAYMENT_PENDING){
                $this->setCellValue('K'.$row, 'PAYMENT_PENDING');
            }
            if($order->status == Order::STATUS_PAYMENT_DONE){
                $this->setCellValue('K'.$row, 'PAYMENT_DONE');
            }
            if($order->status == Order::STATUS_SHIPPED){
                $this->setCellValue('K'.$row, 'SHIPPED');
            }
            
            $this->setCellValue('L'.$row, $order->order_date);
            
            $row++;
        }

        $this->currentRow = $row;

        $this->setCellValue('I'.$row, "=SUM(I1:I{$row})");
        $this->setCellValue('J'.$row, "=SUM(J1:J{$row})");
        $this->mergeCells("A{$row}:G{$row}");
        $this->setBackgroundColor("A{$row}:K{$row}", 'adfb84');
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

        $this->setColumnWidth('H', 25);
        $this->setCellValue('H1', 'Battery/Perfume/Flameable');

        $this->setColumnWidth('I', 20);
        $this->setCellValue('I1', 'Kg');
        
        $this->setColumnWidth('J', 20);
        $this->setCellValue('J1', 'Lbs');
        
        $this->setColumnWidth('K', 20);
        $this->setCellValue('K1', 'Status');

        $this->setColumnWidth('L', 20);
        $this->setCellValue('L1', 'Date');

        $this->setBackgroundColor('A1:L1', '2b5cab');
        $this->setColor('A1:L1', 'FFFFFF');

        $this->currentRow++;
    }
}
