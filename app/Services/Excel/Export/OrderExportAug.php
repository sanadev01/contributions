<?php

namespace App\Services\Excel\Export;
use Illuminate\Support\Collection;
use App\Models\Order;
use App\Services\Calculators\WeightCalculator;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class OrderExportAug extends AbstractExportService
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
            $this->setCellValue('D'.$row, $order->corrios_tracking_code);
            $this->setCellValue('E'.$row, $order->gross_total);
            $this->setCellValue('F'.$row, $order->getOriginalWeight('kg'));
            $this->setCellValue('G'.$row, $order->getWeight('kg'));
            $this->setCellValue('H'.$row, $this->rate($order));
            
            $row++;
        }

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
        $this->setCellValue('D1', 'Tracking Code');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'Amount');

        $this->setColumnWidth('F', 20);
        $this->setCellValue('F1', 'Weight(Kg)');

        $this->setColumnWidth('G', 20);
        $this->setCellValue('G1', 'Metric Weight(kg)');

        $this->setColumnWidth('H', 20);
        $this->setCellValue('H1', 'Correct Amount');

        

        $this->setBackgroundColor('A1:P1', '2b5cab');
        $this->setColor('A1:P1', 'FFFFFF');

        $this->currentRow++;
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

    public function rate($order){
        $newOrder = new Order();
        $newOrder->id = $order->id;
        $newOrder->user = $order->user;
        $newOrder->width = $order->width;
        $newOrder->height = $order->height;
        $newOrder->length = $order->length;
        $newOrder->weight = $order->weight;
        $newOrder->measurement_unit = $order->measurement_unit;
        //Set Volumetric Discount
        $totalDiscountPercentage = 0;
        $totalDiscountedWeight = 0;
        $volumetricDiscount = setting('volumetric_discount', null, $order->user->id);
        $discountPercentage = setting('discount_percentage', null, $order->user->id);
        
        if ( $newOrder->measurement_unit == 'kg/cm' ){
            $volumetricWeight = WeightCalculator::getVolumnWeight($newOrder->length,$newOrder->width,$newOrder->height,'cm');
        }else {
            $volumetricWeight = WeightCalculator::getVolumnWeight($newOrder->length,$newOrder->width,$newOrder->height,'in');
        }
        $volumeWeight = round($volumetricWeight > $newOrder->weight ? $volumetricWeight : $newOrder->weight,2);
        
        if ($volumetricDiscount && $discountPercentage && $discountPercentage > 0 && $discountPercentage != 0) {
            $totalDiscountPercentage = ($discountPercentage) ? $discountPercentage/100 : 0;
        }

        if ($volumeWeight > $newOrder->weight && $totalDiscountPercentage > 0) {
            
            $consideredWeight = $volumeWeight - $newOrder->weight;
            $volumeWeight = round($consideredWeight - ($consideredWeight * $totalDiscountPercentage), 2);
            $totalDiscountedWeight = $consideredWeight - $volumeWeight;
        }
        $newOrder->weight_discount = $totalDiscountedWeight;
        $newOrder->recipient = $order->recipient;

        $rate = 0;
        $service = $order->shippingService;
        if($service){
            $service->cacheCalculator = false;
            if ( $service->isAvailableFor($newOrder) ){
                $rate = $service->getRateFor($newOrder,true,true);
            }
        }

        return $rate;
    }

   
}
