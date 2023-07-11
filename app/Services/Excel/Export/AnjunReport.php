<?php

namespace App\Services\Excel\Export;

use App\Models\Order;
use App\Models\ShippingService;
use Illuminate\Support\Collection;
use App\Models\Warehouse\AccrualRate;

class AnjunReport extends AbstractExportService
{
    private $orders;
    private $request;

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
            $this->setCellValue('A'.$row, $order->order_date);
            $this->setCellValue('B'.$row, $order->warehouse_number);
            $this->setCellValue('C'.$row, $order->user->name);
            $this->setCellValue('D'.$row, $order->corrios_tracking_code);
            $this->setCellValue('E'.$row, optional(optional($order->containers)[0])->unit_code);
            $this->setCellValue('F'.$row, round($order->gross_total,2));
            $this->setCellValue('G'.$row, $this->getValuePaidToCorrieos($order)['airport']);
            $this->setCellValue('H'.$row, $this->getValuePaidToCorrieos($order)['commission']);
            $this->setCellValue('I'.$row, $order->status_name);
            $row++;
        }

        $this->currentRow = $row;

        $this->setCellValue('F'.$row, "=SUM(F1:F{$row})");
        $this->setCellValue('G'.$row, "=SUM(G1:G{$row})");
        $this->setCellValue('H'.$row, "=SUM(H1:H{$row})");
        $this->setBackgroundColor("A{$row}:H{$row}", 'adfb84');
    }

    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'Date');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'Warehouse No.');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'User Name');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'Tracking Code');
        
        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'Unit Code');

        $this->setColumnWidth('F', 20);
        $this->setCellValue('F1', 'Amount Customers Paid');

        $this->setColumnWidth('G', 20);
        $this->setCellValue('G1', 'Correios (Anjun)');

        $this->setColumnWidth('H', 20);
        $this->setCellValue('H1', 'Anjun Commission');
        
        $this->setColumnWidth('I', 20);
        $this->setCellValue('I1', 'Status');

        $this->setBackgroundColor('A1:I1', '2b5cab');
        $this->setColor('A1:I1', 'FFFFFF');

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
