<?php

namespace App\Services\Excel\Export;
use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ScanOrderExport extends AbstractExportService
{
    private $orders;

    private $pieces;

    private $totalWeight = 0;

    private $currentRow = 1;

    private $count = 1;

    public function __construct(Collection $orders)
    {
        $this->orders = $orders;
        $this->pieces = $this->orders->count();
        $this->totalWeight = $this->calculateTotalWeight();

        parent::__construct();
    }

    public function handle()
    {
        $this->prepareExcelSheet();

        return $this->download();
    }

    
    public function getFilePath()
    {
        $this->prepareExcelSheet();

        return $this->downloadExcel();
    }

    private function prepareExcelSheet()
    {
        $this->setExcelHeaderRow();

        $row = $this->currentRow;

        $this->setSecondHeaderRow($row);

        $row = $this->currentRow;

        foreach ($this->orders as $order) {
            $this->setCellValue('A'.$row, $this->count);
            $this->setCellValue('B'.$row, $order->corrios_tracking_code);
            $this->setCellValue('C'.$row, $order->user->pobox_number);
            $this->setCellValue('D'.$row, $order->merchant);
            $this->setCellValue('E'.$row, $order->length . ' x ' . $order->length . ' x ' . $order->height );
            $this->setCellValue('F'.$row, $this->chargeWeight($order));
            $this->setCellValue('G'.$row, number_format($order->getWeight('kg'),2));
            $this->setCellValue('H'.$row, $order->id);
            $this->setCellValue('I'.$row, $order->tracking_id);
            $this->setCellValue('J'.$row, $order->recipient->first_name);
            $this->setCellValue('K'.$row, $order->order_date->format('m-d-Y'));
            $this->setCellValue('L'.$row, $order->arrived_date);
            $this->setCellValue('M'.$row, optional(optional($order->driverTracking)->user)->name);
            $this->setCellValue('N'.$row, optional(optional($order->driverTracking)->created_at)->format('m-d-Y'));
            $this->setCellValue('O'.$row, $order->status < 80 ?'Scanned in the warehouse':'Shipped');
            $this->setCellValue('P'.$row, $order->customer_reference);
            
            $this->count++ ;
            $row++;
        }

        $this->currentRow = $row;

    }


    private function setExcelHeaderRow()
    {

        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', '');

        $this->setColumnWidth('B', 25);
        $this->setCellValue('B1', 'Homedeliverybr');
        $this->setCellValue('B2', '2200 NW 129th Avenue');
        $this->setCellValue('B3', 'Suite#100');
        $this->setCellValue('B4', 'Miami, FL 33182');

        $this->setCellValue('B6', 'Report Date:');
        $this->setBold('B6', true);
        $this->setColor('B6', 'FF0D0D');

        $this->setColumnWidth('C', 25);
        $this->setCellValue('C6', Carbon::now()->toFormattedDateString());
        $this->setBold('C6', true);
        $this->setColor('C6', '0A0000');

        $this->setColumnWidth('D', 25);
        $this->setCellValue('D1', 'Pieces : '.$this->pieces);
        $this->setCellValue('D2', 'Total Weight : '.$this->totalWeight.' Kg');
        
        $this->setCellValue('D6', Carbon::now()->format('d/m/Y H:i:s'));
        
        $this->setBackgroundColor('A6:D6', 'C4C2C2');

        $this->currentRow = 7;
    }

    private function setSecondHeaderRow($row)
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A7', 'Nr. Boxes');
        
        $this->setColumnWidth('B', 20);
        $this->setCellValue('B7', 'Tracking Code');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C7', 'POBOX#');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D7', 'Client');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E7', 'Dimensions');

        $this->setColumnWidth('F', 20);
        $this->setCellValue('F7', 'Weight Kg');

        $this->setColumnWidth('G', 20);
        $this->setCellValue('G7', 'Metric Weight(kg)');
        
        $this->setColumnWidth('H', 20);
        $this->setCellValue('H7', 'Reference#');
        
        $this->setColumnWidth('I', 20);
        $this->setCellValue('I7', 'Carrier Tracking');

        $this->setColumnWidth('J', 20);
        $this->setCellValue('J7', 'Recpient');

        $this->setColumnWidth('K', 20);
        $this->setCellValue('K7', 'Order Date');
        
        $this->setColumnWidth('L', 20);
        $this->setCellValue('L7', 'Arrival Date');

        $this->setColumnWidth('M', 20);
        $this->setCellValue('M7', 'Driver');

        $this->setColumnWidth('N', 20);
        $this->setCellValue('N7', 'Pickup Date');
        
        $this->setColumnWidth('O', 20);
        $this->setCellValue('O7', 'Status');
        $this->setColumnWidth('P', 20);
        $this->setCellValue('P7', 'Additional Reference');

        $this->setBackgroundColor('A7:P7', '2b5cab');
        $this->setColor('A7:P7', 'FFFFFF');
        $this->currentRow++;

        return true;
    }

    private function calculateTotalWeight()
    {
        $totalWeight = 0;

        foreach ($this->orders as $order) {
            $totalWeight += $order->getWeight('kg');
        }

        return number_format((float)$totalWeight, 2, '.', '');
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
}
