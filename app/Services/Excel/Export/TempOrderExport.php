<?php

namespace App\Services\Excel\Export;
use App\Models\User;
use App\Models\Order;
use App\Models\ShippingService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

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
            $totalAmount = $order->items->reduce(function ($carry, $orderItem) {
                return $carry + ($orderItem->quantity * $orderItem->value);
            }, 0);
    
            $this->setCellValue('A'.$row, $order->getSenderFullName());
            $this->setCellValue('B'.$row, $order->recipient->getFullName()); 
            $this->setCellValue('D'.$row, (string)$this->getOrderTrackingCodes($order)); 
            $this->setCellValue('E'.$row, (string)$this->chargeWeight($order)); 
            $this->setCellValue('F'.$row, $order->shipping_value); 
            $this->setCellValue('G'.$row, $order->user->pobox_number); 
            $this->setCellValue('H'.$row, $order->user_declared_freight); 
            $this->setCellValue('I'.$row, number_format($totalAmount, 2));
            $orderStatus = !empty($order->deleted_at) ? "Order is Deleted on " . date('Y-m-d', strtotime($order->deleted_at)) : '';
            $this->setCellValue('K'.$row, $orderStatus);
            $this->setColor('K', 'FF0000');
            
            foreach($order->items as $item) { 
                $this->setCellValue('J'.$row, $item->description);   
                $this->setCellValue('C'.$row, $item->sh_code);   
                $row++;
            }
    
            $row++;
        } 

    }

    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 30);
        $this->setCellValue('A1', 'sender');

        $this->setColumnWidth('B', 30);
        $this->setCellValue('B1', 'receiver#');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'NCM (HS#)');
        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'Tracking');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'weight');

        $this->setColumnWidth('F', 20);
        $this->setCellValue('F1', 'shipping paid');  
        $this->setColumnWidth('G', 20);
        $this->setCellValue('G1', 'PO Box Number');    
        $this->setColumnWidth('H', 20);
        $this->setCellValue('H1', 'ttl declared value');  
        
        $this->setColumnWidth('I', 30);
        $this->setCellValue('I1', 'Order Value');

        $this->setColumnWidth('J', 30);
        $this->setCellValue('J1', 'Description of product');

        $this->setColumnWidth('K', 30);
        $this->setCellValue('K1', 'Status');

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
