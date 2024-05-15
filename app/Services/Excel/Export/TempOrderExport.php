<?php

namespace App\Services\Excel\Export;

use Illuminate\Support\Collection;

class TempOrderExport extends AbstractExportService
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

        return $this->downloadExcel();
    }

    private function prepareExcelSheet()
    {
        $this->setExcelHeaderRow();

        $row = $this->currentRow;

        foreach ($this->orders as $order) {

            if ($order instanceof Order) {

                $totalAmount = $order->items->reduce(function ($carry, $orderItem) {
                    return $carry + ($orderItem->quantity * $orderItem->value);
                }, 0);

                $this->setCellValue('A'.$row, $order->corrios_tracking_code);
                $this->setCellValue('B'.$row, $order->warehouse_number); 
                $this->setCellValue('C'.$row, optional($order->user)->pobox_number); 
                $this->setCellValue('D'.$row, !empty($order->deleted_at) ? "Order is Deleted on " . date('Y-m-d', strtotime($order->deleted_at)) : '');
                $this->setCellValue('E'.$row, $order->getSenderFullName());
                $this->setCellValue('F'.$row, optional($order->recipient)->getFullName());
                $this->setCellValue('G'.$row, $this->chargeWeight($order));
                $this->setCellValue('H'.$row, $order->length.'x'.$order->width.'x'.$order->height);
                $this->setCellValue('I'.$row, $order->shipping_value);
                $this->setCellValue('J'.$row, number_format($totalAmount,2));
                
                foreach($order->items as $item) {
                    $this->setCellValue('K'.$row, $item->sh_code);   
                    $this->setCellValue('L'.$row, $item->description);
                    $row++; 
                }
                $this->setColor('D', 'FF0000');
            } else {

                $this->setCellValue('A'.$row, $order->corrios_tracking_code);
                $this->setCellValue('B'.$row, $order->warehouse_number); 
                $this->setCellValue('C'.$row, optional($order->user)->pobox_number); 
                $this->setCellValue('D'.$row, "Order Not Found");

                $this->setCellValue('K'.$row, '');   
                $this->setCellValue('L'.$row, '');
                $this->setBackgroundColor("A{$row}:L{$row}", 'FF0000');
                $row++;
            }
        }
    }


    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 30);
        $this->setCellValue('A1', 'Tracking Code');

        $this->setColumnWidth('B', 30);
        $this->setCellValue('B1', 'Warehouse No.');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'POBOX');
        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'Status');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'Sender');

        $this->setColumnWidth('F', 20);
        $this->setCellValue('F1', 'Recepient');  
        $this->setColumnWidth('G', 20);
        $this->setCellValue('G1', 'Weight');
        $this->setColumnWidth('H', 20);
        $this->setCellValue('H1', 'Dimensions');  

        $this->setColumnWidth('I', 20);
        $this->setCellValue('I1', 'Shipping Paid');  
        
        $this->setColumnWidth('J', 30);
        $this->setCellValue('J1', 'Order Value');

        $this->setColumnWidth('K', 30);
        $this->setCellValue('K1', 'NCM');

        $this->setColumnWidth('L', 30);
        $this->setCellValue('L1', 'Description of Product');


        $this->currentRow++;
    }
    public function isWeightInKg($measurement_unit)
    {
        return $measurement_unit == 'kg/cm' ? 'kg' : 'lbs';
    }

    public function chargeWeight($order)
    {
        $getOriginalWeight = $order->getOriginalWeight('kg');
        $chargeWeight = $getOriginalWeight;
        $getWeight = $order->getWeight('kg');
        if ($getWeight > $getOriginalWeight && $order->weight_discount) {
            $discountWeight = $order->weight_discount;
            if ($order->measurement_unit == 'lbs/in') {
                $discountWeight = $order->weight_discount / 2.205;
            }
            $consideredWeight = $getWeight - $getOriginalWeight;
            $chargeWeight = ($consideredWeight - $discountWeight) + $getOriginalWeight;
        }

        return round($chargeWeight, 2);
    }

    private function getOrderTrackingCodes($order)
    {
        $trackingCodes = ($order->has_second_label ? $order->corrios_tracking_code . ',' . $order->us_api_tracking_code : $order->corrios_tracking_code);
        return (string)$trackingCodes;
    }
}
