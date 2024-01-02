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
            $this->setCellValue('A'.$row, $order->getSenderFullName());
            $this->setCellValue('B'.$row, $order->recipient->getFullName()); 
            $this->setCellValue('C'.$row, (string)$this->getOrderTrackingCodes($order)); 
            $this->setCellValue('D'.$row, (string)$this->chargeWeight($order)); 
            $this->setCellValue('E'.$row, $order->shipping_value); 
            $this->setCellValue('F'.$row, $order->user_declared_freight); 
            foreach($order->items as $item) { 
                $this->setCellValue('G'.$row, $item->description);   
                $row++;
            } 
        } 

    }

    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 30);
        $this->setCellValue('A1', 'sender');

        $this->setColumnWidth('B', 30);
        $this->setCellValue('B1', 'receiver#');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'Tracking');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'weight');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'shipping paid');  
        $this->setColumnWidth('F', 20);
        $this->setCellValue('F1', 'ttl declared value');  
        $this->setColumnWidth('G', 30);
        $this->setCellValue('G1', 'Description of product');    

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
        if($getWeight > $getOriginalWeight && $order->weight_discount){
            $discountWeight = $order->weight_discount;
            if($order->measurement_unit == 'lbs/in'){
                $discountWeight = $order->weight_discount/2.205;
            }
            $consideredWeight = $getWeight - $getOriginalWeight;
            $chargeWeight = ($consideredWeight - $discountWeight) + $getOriginalWeight;
        }
        
        return round($chargeWeight,2);
    }

    private function getOrderTrackingCodes($order)
    {
        $trackingCodes = ($order->hasSecondLabel() ? $order->corrios_tracking_code.','.$order->us_api_tracking_code : $order->corrios_tracking_code);
        return (string)$trackingCodes;
    } 
}
