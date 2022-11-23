<?php

namespace App\Services\Excel\Export;
use App\Models\Order;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
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
            $this->setCellValue('G'.$row, (string)$this->getOrderTrackingCodes($order));
            $this->setCellValue('H'.$row, $order->gross_total);
            $this->setCellValue('I'.$row, optional($order->us_secondary_label_cost)['profit_cost']);
            $this->setCellValue('J'.$row, $this->checkValue(number_format($order->dangrous_goods,2)));
            $this->setCellValue('K'.$row, $this->chargeWeight($order));
            $this->setCellValue('L'.$row, $order->getWeight('kg'));
            $this->setCellValue('M'.$row, round(($this->chargeWeight($order)*2.205),2));
            $this->setCellValue('N'.$row, $order->getWeight('lbs'));
            $this->setCellValue('O'.$row, $order->length. ' X '. $order->width.' X '.$order->height);

            if($order->status == Order::STATUS_ORDER){
                $this->setCellValue('P'.$row, 'ORDER');
            }
            if($order->status == Order::STATUS_NEEDS_PROCESSING){
                $this->setCellValue('P'.$row, 'PROCESSING');
            }
            if($order->status == Order::STATUS_CANCEL){
                $this->setCellValue('P'.$row, 'CANCEL');
            }
            if($order->status == Order::STATUS_REJECTED){
                $this->setCellValue('P'.$row, 'REJECTED');
            }
            if($order->status == Order::STATUS_RELEASE){
                $this->setCellValue('P'.$row, 'RELEASED');
            }
            if($order->status == Order::STATUS_REFUND){
                $this->setCellValue('P'.$row, 'REFUND');
            }
            if($order->status == Order::STATUS_PAYMENT_PENDING){
                $this->setCellValue('P'.$row, 'PAYMENT PENDING');
            }
            if($order->status == Order::STATUS_PAYMENT_DONE){
                $this->setCellValue('P'.$row, 'PAYMENT DONE');
            }
            if($order->status == Order::STATUS_SHIPPED){
                $this->setCellValue('P'.$row, 'SHIPPED');
            }

            $this->setCellValue('Q'.$row, $order->weight_discount);
            $this->setCellValue('R'.$row, $order->discountCost());
            
            $this->setCellValue('S'.$row, $this->getcarrier($order)['intl']);
            $this->setCellValue('T'.$row, $this->getcarrier($order)['domestic']);

            if(Auth::user()->isAdmin()){
                
                $this->setCellValue('U'.$row, $order->carrierCost());
                $this->setCellValue('V'.$row, optional($order->us_secondary_label_cost)['api_cost']);
                $this->setCellValue('W'.$row,setting('marketplace_checked', null, $user->id)?  setting('marketplace', null, $user->id):'');
            }

            
            $row++;
        }

        $this->currentRow = $row;

        $this->setCellValue('H'.$row, "=SUM(H1:H{$row})");
        $this->setCellValue('I'.$row, "=SUM(I1:I{$row})");
        $this->setCellValue('J'.$row, "=SUM(J1:J{$row})");
        $this->setCellValue('K'.$row, "=SUM(K1:K{$row})");
        $this->setCellValue('L'.$row, "=SUM(L1:L{$row})");
        $this->setCellValue('M'.$row, "=SUM(M1:M{$row})");
        $this->setCellValue('Q'.$row, "=SUM(Q1:Q{$row})");
        $this->setCellValue('R'.$row, "=SUM(R1:R{$row})");

        
        $this->mergeCells("A{$row}:F{$row}");
        $this->setBackgroundColor("A{$row}:W{$row}", 'adfb84');
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

        $this->setColumnWidth('H', 25);
        $this->setCellValue('H1', 'Customer Paid 1st Label');

        $this->setColumnWidth('I', 25);
        $this->setCellValue('I1', 'Customer Paid 2nd Label');
        
        $this->setColumnWidth('J', 25);
        $this->setCellValue('J1', 'Battery/Perfume/Flameable');

        $this->setColumnWidth('K', 20);
        $this->setCellValue('K1', 'Weight(Kg)');
        
        $this->setColumnWidth('L', 20);
        $this->setCellValue('L1', 'Metric Weight(kg)');

        $this->setColumnWidth('M', 20);
        $this->setCellValue('M1', 'Weight(Lbs)');
        
        $this->setColumnWidth('N', 20);
        $this->setCellValue('N1', 'Metric Weight(Lbs)');

        $this->setColumnWidth('O', 20);
        $this->sheet->getStyle('O')->getAlignment()->setHorizontal('center');
        $this->setCellValue('O1', 'Dimesnsions');

        $this->setColumnWidth('P', 20);
        $this->setCellValue('P1', 'Status');

        $this->setColumnWidth('Q', 20);
        $this->setCellValue('Q1', 'Discount Weight');

        $this->setColumnWidth('R', 20);
        $this->setCellValue('R1', 'Discount Amount');

        $this->setColumnWidth('S', 20);
        $this->setCellValue('S1', 'Intl Carrier Service');
        
        $this->setColumnWidth('T', 20);
        $this->setCellValue('T1', 'Domestic Carrier Service');

        if(Auth::user()->isAdmin()){
            $this->setColumnWidth('U', 20);
            $this->setCellValue('U1', '1st Label Cost');

            $this->setColumnWidth('V', 20);
            $this->setCellValue('V1', '2nd Label Cost');

            $this->setColumnWidth('W', 20);
            $this->setCellValue('W1', 'Marketplace');
        }

        $this->setBackgroundColor('A1:W1', '2b5cab');
        $this->setColor('A1:V1', 'FFFFFF');

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
        $trackingCodes = ($order->hasSecondLabel() ? $order->corrios_tracking_code.','.$order->us_api_tracking_code : $order->corrios_tracking_code." ");
        return (string)$trackingCodes;
    }
    
    private function getcarrier($order)
    {
        if(in_array($order->carrierService(), ['USPS','UPS','FEDEX']) ){
            return [
                'intl' => null,
                'domestic' => $order->carrierService()
            ];
        }
        
        if(in_array($order->carrierService(), ['Correios Chile', 'GePS', 'Correios Brazil']) ){
            return [
                'intl' => $order->carrierService(),
                'domestic' => $order->secondCarrierAervice()
            ];
        }
        
        
    }
}
