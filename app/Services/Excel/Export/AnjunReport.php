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
                    $shippingService = $order->shippingService;
                    $commission = $this->getValuePaidToCorrieos($order);
                    if ($shippingService) {
                        $this->setCellValue('A' . $row, $order->order_date);
                        $this->setCellValue('B' . $row, $order->warehouse_number);
                        $this->setCellValue('C' . $row, $order->user->name);
                        $this->setCellValue('D' . $row, $order->corrios_tracking_code);
                        $this->setCellValue('E' . $row, $order->getOriginalWeight('kg') . 'kg');
                        $this->setCellValue('F' . $row, $shippingService->sub_name);
                        $this->setCellValue('G' . $row, optional(optional($order->containers)[0])->unit_code);
                        $this->setCellValue('H' . $row, round($order->gross_total, 2));
                        // $this->setCellValue('I' . $row, $commission['airport']);
                        $this->setCellValue('I' . $row, $commission['commission']);
                        $this->setCellValue('J' . $row, $order->status_name);
                        $this->setCellValue('K' . $row, $deliveryBill->created_at);
                        $row++;
                    }
                }
            }
        }

        $this->currentRow = $row;
        $this->setCellValue('H' . $row, "=SUM(H1:H{$row})");
        $this->setCellValue('I' . $row, "=SUM(I1:I{$row})");
        $this->setBackgroundColor("A{$row}:I{$row}", 'adfb84');
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
        $this->setCellValue('F1', 'Correios');

        $this->setColumnWidth('G', 20);
        $this->setCellValue('G1', 'Unit Code');

        $this->setColumnWidth('H', 20);
        $this->setCellValue('H1', 'Gross total');

        // $this->setColumnWidth('I', 20);
        // $this->setCellValue('I1', 'Airport');

        $this->setColumnWidth('I', 20);
        $this->setCellValue('I1', 'Anjun Commission');

        $this->setColumnWidth('J', 20);
        $this->setCellValue('J1', 'Status');

        $this->setColumnWidth('K', 20);
        $this->setCellValue('K1', 'DeliveryBill Date');

        $this->setBackgroundColor('A1:K1', '2b5cab');
        $this->setColor('A1:K1', 'FFFFFF');

        $this->currentRow++;
    }

    protected function getValuePaidToCorrieos(Order $order)
    {
        $commission = false;
        $service  = $order->shippingService->service_sub_class;
        $rateSlab = AccrualRate::getRateSlabFor($order->getOriginalWeight('kg'), $service);

        if (!$rateSlab) {
            return [
                'airport' => 0,
                'commission' => 0
            ];
        }
        if ($service == ShippingService::AJ_Packet_Standard || $service == ShippingService::AJ_Packet_Express) {
            $commission = true;
        }
        if ($service == ShippingService::AJ_Express_CN || $service == ShippingService::AJ_Standard_CN) {
            $commission = true;
        }
        return [
            'airport' => $rateSlab->cwb,
            'commission' => $commission ? $rateSlab->commission : 0
        ];
    }
}
