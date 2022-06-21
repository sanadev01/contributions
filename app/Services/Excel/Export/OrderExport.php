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
        
            $this->setCellValue('A'.$row, $order->order_date);
            $this->setCellValue('B'.$row, $order->warehouse_number);
            $this->setCellValue('C'.$row, $user->name);
            $this->setCellValue('D'.$row, $order->merchant);
            $this->setCellValue('E'.$row, $order->tracking_id);
            $this->setCellValue('F'.$row, $order->customer_reference);
            $this->setCellFormat('G'.$row);
            $this->setCellValue('G'.$row, $this->getOrderTrackingCodes($order));
            $this->setCellValue('H'.$row, $order->gross_total);
            $this->setCellValue('I'.$row, $this->checkValue(number_format($order->dangrous_goods,2)));
            $this->setCellValue('J'.$row, $order->getWeight('kg'));
            $this->setCellValue('K'.$row, $order->getWeight('lbs'));
            $this->setCellValue('L'.$row, $this->getVolumnWeight($order->length, $order->width, $order->height,$this->isWeightInKg($order->measurement_unit)));
            $this->setCellValue('M'.$row, $order->length. ' X '. $order->width.' X '.$order->height);

            if($order->status == Order::STATUS_ORDER){
                $this->setCellValue('N'.$row, 'ORDER');
            }
            if($order->status == Order::STATUS_NEEDS_PROCESSING){
                $this->setCellValue('N'.$row, 'PROCESSING');
            }
            if($order->status == Order::STATUS_CANCEL){
                $this->setCellValue('N'.$row, 'CANCEL');
            }
            if($order->status == Order::STATUS_REJECTED){
                $this->setCellValue('N'.$row, 'REJECTED');
            }
            if($order->status == Order::STATUS_RELEASE){
                $this->setCellValue('N'.$row, 'RELEASED');
            }
            if($order->status == Order::STATUS_REFUND){
                $this->setCellValue('N'.$row, 'REFUND');
            }
            if($order->status == Order::STATUS_PAYMENT_PENDING){
                $this->setCellValue('N'.$row, 'PAYMENT PENDING');
            }
            if($order->status == Order::STATUS_PAYMENT_DONE){
                $this->setCellValue('N'.$row, 'PAYMENT DONE');
            }
            if($order->status == Order::STATUS_SHIPPED){
                $this->setCellValue('N'.$row, 'SHIPPED');
            }
            
            
            $row++;
        }

        $this->currentRow = $row;

        $this->setCellValue('H'.$row, "=SUM(H1:H{$row})");
        $this->setCellValue('I'.$row, "=SUM(I1:I{$row})");
        $this->setCellValue('J'.$row, "=SUM(J1:J{$row})");
        $this->setCellValue('K'.$row, "=SUM(K1:K{$row})");
        $this->setCellValue('L'.$row, "=SUM(L1:L{$row})");
        $this->mergeCells("A{$row}:F{$row}");
        $this->setBackgroundColor("A{$row}:N{$row}", 'adfb84');
        $this->setAlignment('A'.$row, Alignment::VERTICAL_CENTER);
        $this->setCellValue('A'.$row, 'Total Order: '.$this->orders->count());



    }


    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'Date');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'Order ID#');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'Name');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'Loja/Cliente');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'Carrier Tracking');

        $this->setColumnWidth('F', 20);
        $this->setCellValue('F1', 'Referência do Cliente');

        $this->setColumnWidth('G', 23);
        $this->setCellValue('G1', '	Tracking Code');

        $this->setColumnWidth('H', 20);
        $this->setCellValue('H1', 'Amount');

        $this->setColumnWidth('I', 25);
        $this->setCellValue('I1', 'Battery/Perfume/Flameable');

        $this->setColumnWidth('J', 20);
        $this->setCellValue('J1', 'Weight(Kg)');
        
        $this->setColumnWidth('K', 20);
        $this->setCellValue('K1', 'Weight(Lbs)');

        $this->setColumnWidth('L', 20);
        $this->setCellValue('L1', 'Metric Weight(kg)');

        $this->setColumnWidth('M', 20);
        $this->sheet->getStyle('M')->getAlignment()->setHorizontal('center');
        $this->setCellValue('M1', 'Dimesnsions');

        $this->setColumnWidth('N', 20);
        $this->setCellValue('N1', 'Status');

        

        $this->setBackgroundColor('A1:N1', '2b5cab');
        $this->setColor('A1:N1', 'FFFFFF');

        $this->currentRow++;
    }

    private function checkValue($value)
    {
        if($value == 0){
            return '';
        }

        return $value;
    }

    public function isWeightInKg($measurement_unit)
    {
        return $measurement_unit == 'kg/cm' ? 'cm' : 'in';
    }

    public function getVolumnWeight($length, $width, $height, $unit)
    {
        $divisor = $unit == 'in' ? 166 : 6000;
        return round(($length * $width * $height) / $divisor,2);
    }

    private function getOrderTrackingCodes($order)
    {
        $trackingCodes = ($order->hasSecondLabel() ? $order->corrios_tracking_code.','.$order->us_api_tracking_code : $order->corrios_tracking_code);
        return (string)$trackingCodes;
    }
}
