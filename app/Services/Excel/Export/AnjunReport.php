<?php

namespace App\Services\Excel\Export;

use App\Models\Order;
use App\Models\ShippingService;
use Illuminate\Support\Collection;
use App\Models\Warehouse\AccrualRate;

class AnjunReport extends AbstractExportService
{
    private $deliveryBills;
    private $request;

    private $currentRow = 1;

    public function __construct(Collection $deliveryBills)
    {
        $this->deliveryBills = $deliveryBills;

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
        foreach ($this->deliveryBills as $deliveryBill) {
            foreach ($deliveryBill->containers as $container) {
                foreach ($container->orders as $order) {
                    if($order->shippingService){
                        if($order->shippingService->is_correios)
                        {
                            $service  = $order->shippingService;

                            $this->setCellValue('A'.$row, $order->order_date);
                            $this->setCellValue('B'.$row, $order->warehouse_number);
                            $this->setCellValue('C'.$row, $order->user->name);
                            $this->setCellValue('D'.$row, $order->corrios_tracking_code);
                            $this->setCellValue('E'.$row, $order->getWeight());
                            $this->setCellValue(
                                'F' . $row, 
                                 $service->sub_name
                            );
                            $this->setCellValue('G'.$row, optional(optional($order->containers)[0])->unit_code);
                            $this->setCellValue('H'.$row, round($order->gross_total,2));
                            $this->setCellValue('I'.$row, $this->getValuePaidToCorrieos($order)['airport']);
                            $this->setCellValue('J'.$row, $this->getValuePaidToCorrieos($order)['commission']);
                            $this->setCellValue('K'.$row, $order->status_name);
                            $this->setCellValue('L'.$row, $deliveryBill->created_at);
                            $row++;
                        }
                    }
                }
            }
        }

        $this->currentRow = $row;

        $this->setCellValue('H'.$row, "=SUM(H1:H{$row})");
        $this->setCellValue('I'.$row, "=SUM(I1:I{$row})");
        $this->setCellValue('J'.$row, "=SUM(J1:J{$row})");
        $this->setBackgroundColor("A{$row}:L{$row}", 'adfb84');
    }

    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'Order Create Date');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'Warehouse No.');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'User Name');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'Tracking Code');
        
        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'Weight');

        $this->setColumnWidth('F', 20);
        $this->setCellValue('F1', 'Service');

        $this->setColumnWidth('G', 20);
        $this->setCellValue('G1', 'Unit Code');

        $this->setColumnWidth('H', 20);
        $this->setCellValue('H1', 'Amount Customers Paid');

        $this->setColumnWidth('I', 20);
        $this->setCellValue('I1', 'Correios');

        $this->setColumnWidth('J', 20);
        $this->setCellValue('J1', 'Anjun Commission');
        
        $this->setColumnWidth('K', 20);
        $this->setCellValue('K1', 'Status');

        $this->setColumnWidth('L', 20);
        $this->setCellValue('L1', 'DeliveryBill Date');

        $this->setBackgroundColor('A1:L1', '2b5cab');
        $this->setColor('A1:L1', 'FFFFFF');

        $this->currentRow++;

    }

    protected function getValuePaidToCorrieos(Order $order)
    {
        $commission = false;
        $service  = $order->shippingService->service_sub_class;
        $rateSlab = AccrualRate::getRateSlabFor($order->getOriginalWeight('kg'),$service);

        if ( !$rateSlab ){
            return [
                'airport'=> 0,
                'commission'=> 0
            ];
        }
        if($service == ShippingService::AJ_Packet_Standard || $service == ShippingService::AJ_Packet_Express){
            $commission = true;
        }
        return [
            'airport'=> $rateSlab->cwb,
            'commission'=> $commission ? $rateSlab->commission : 0
        ];
    }
}
