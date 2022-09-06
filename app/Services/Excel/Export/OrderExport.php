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
            $this->setCellValue('G'.$row, $this->getOrderTrackingCodes($order));
            $this->setCellValue('H'.$row, $order->gross_total);
            $this->setCellValue('I'.$row, $this->checkValue(number_format($order->dangrous_goods,2)));
            $this->setCellValue('J'.$row, $this->chargeWeight($order));
            $this->setCellValue('K'.$row, $order->getWeight('kg'));
            $this->setCellValue('L'.$row, round(($this->chargeWeight($order)*2.205),2));
            $this->setCellValue('M'.$row, $order->getWeight('lbs'));
            $this->setCellValue('N'.$row, $order->length. ' X '. $order->width.' X '.$order->height);

            if($order->status == Order::STATUS_ORDER){
                $this->setCellValue('O'.$row, 'ORDER');
            }
            if($order->status == Order::STATUS_NEEDS_PROCESSING){
                $this->setCellValue('O'.$row, 'PROCESSING');
            }
            if($order->status == Order::STATUS_CANCEL){
                $this->setCellValue('O'.$row, 'CANCEL');
            }
            if($order->status == Order::STATUS_REJECTED){
                $this->setCellValue('O'.$row, 'REJECTED');
            }
            if($order->status == Order::STATUS_RELEASE){
                $this->setCellValue('O'.$row, 'RELEASED');
            }
            if($order->status == Order::STATUS_REFUND){
                $this->setCellValue('O'.$row, 'REFUND');
            }
            if($order->status == Order::STATUS_PAYMENT_PENDING){
                $this->setCellValue('O'.$row, 'PAYMENT PENDING');
            }
            if($order->status == Order::STATUS_PAYMENT_DONE){
                $this->setCellValue('O'.$row, 'PAYMENT DONE');
            }
            if($order->status == Order::STATUS_SHIPPED){
                $this->setCellValue('O'.$row, 'SHIPPED');
            }

            $this->setCellValue('P'.$row, $order->weight_discount);

            $this->setCellValue('Q'.$row, $order->discountCost());
            
            
            $row++;
        }

        $this->currentRow = $row;

        $this->setCellValue('H'.$row, "=SUM(H1:H{$row})");
        $this->setCellValue('I'.$row, "=SUM(I1:I{$row})");
        $this->setCellValue('J'.$row, "=SUM(J1:J{$row})");
        $this->setCellValue('K'.$row, "=SUM(K1:K{$row})");
        $this->setCellValue('L'.$row, "=SUM(L1:L{$row})");
        $this->setCellValue('M'.$row, "=SUM(M1:M{$row})");
        $this->setCellValue('P'.$row, "=SUM(P1:P{$row})");
        $this->setCellValue('Q'.$row, "=SUM(Q1:Q{$row})");
        $this->mergeCells("A{$row}:F{$row}");
        $this->setBackgroundColor("A{$row}:Q{$row}", 'adfb84');
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
        $this->setCellValue('K1', 'Metric Weight(kg)');

        $this->setColumnWidth('L', 20);
        $this->setCellValue('L1', 'Weight(Lbs)');
        
        $this->setColumnWidth('M', 20);
        $this->setCellValue('M1', 'Metric Weight(Lbs)');

        $this->setColumnWidth('N', 20);
        $this->sheet->getStyle('N')->getAlignment()->setHorizontal('center');
        $this->setCellValue('N1', 'Dimesnsions');

        $this->setColumnWidth('O', 20);
        $this->setCellValue('O1', 'Status');

        $this->setColumnWidth('P', 20);
        $this->setCellValue('P1', 'Discount Weight');

        $this->setColumnWidth('Q', 20);
        $this->setCellValue('Q1', 'Discount Amount');

        $this->setBackgroundColor('A1:Q1', '2b5cab');
        $this->setColor('A1:Q1', 'FFFFFF');

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
        return $measurement_unit == 'kg/cm' ? 'kg' : 'lbs';
    }

    public function chargeWeight($order)
    {
        $chargeWeight = $order->getOriginalWeight('kg');
        if($order->getWeight('kg') > $order->getOriginalWeight('kg') && $order->weight_discount){
            $discountWeight = $order->weight_discount;
            if($order->measurement_unit == 'lbs/in'){
                $discountWeight = $order->weight_discount/2.205;
            }
            $consideredWeight = $order->getWeight('kg') - $order->getOriginalWeight('kg');
            $chargeWeight = ($consideredWeight - $discountWeight) + $order->getOriginalWeight('kg');
        }
        
        return round($chargeWeight,2);
    }

    private function getOrderTrackingCodes($order)
    {
        $trackingCodes = ($order->hasSecondLabel() ? $order->corrios_tracking_code.','.$order->us_api_tracking_code : $order->corrios_tracking_code);
        return (string)$trackingCodes;
    }
}
