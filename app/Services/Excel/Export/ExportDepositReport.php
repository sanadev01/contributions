<?php

namespace App\Services\Excel\Export;

use App\Models\Order;
use App\Models\ShippingService;
use Illuminate\Support\Collection;
use App\Models\Warehouse\AccrualRate;

class ExportDepositReport extends AbstractExportService
{
    private $deposits;

    private $currentRow = 1;

    public function __construct(Collection $deposits)
    {
        $this->deposits = $deposits;

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

        foreach ($this->deposits as $deposit) {

            $this->setCellValue('A'.$row, $deposit->uuid);
            $this->setCellValue('B'.$row, optional($deposit->getOrder($deposit->order_id))->warehouse_number);
            $this->setCellValue('C'.$row, optional(optional($deposit->getOrder($deposit->order_id))->recipient)->fullName());
            $this->setCellValue('D'.$row, optional($deposit->getOrder($deposit->order_id))->customer_reference);
            $this->setCellValue('E'.$row, ($deposit->firstOrder()) ? optional($deposit->firstOrder())->us_api_tracking_code : optional($deposit->getOrder($deposit->order_id))->corrios_tracking_code);
            $this->setCellValue('F'.$row, $deposit->created_at->format('m/d/Y'));
            $this->setCellValue('G'.$row, $deposit->amount);
            $this->setCellValue('H'.$row, ($deposit->getOrder($deposit->order_id)) ? $this->getShippingCarrier($deposit ,$deposit->getOrder($deposit->order_id)) : '');
            if (auth()->user()->isAdmin()) {
                $this->setCellValue('I'.$row, ($deposit->getOrder($deposit->order_id)) ? $this->getShippingCarrierCost($deposit ,$deposit->getOrder($deposit->order_id)) : '');
            }
            $this->setCellValue('J'.$row, ($deposit->getOrder($deposit->order_id)) ? $this->getOrderDimensions($deposit->getOrder($deposit->order_id)) : '');
            $this->setCellValue('K'.$row, ($deposit->getOrder($deposit->order_id)) ? $this->getOrderTotalWeight($deposit->getOrder($deposit->order_id)) : '');
            $this->setCellValue('L'.$row, ($deposit->getOrder($deposit->order_id)) ? $this->getOrderVolumetricWeight($deposit->getOrder($deposit->order_id)) : '');
            $this->setCellValue('M'.$row, $deposit->isCredit() ? 'Credit' : 'Debit');
            $row++;
        }

        $this->currentRow = $row;
    }

    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'DP#');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'WHR#');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'Recipient');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'Customer Reference#');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'Tracking Code');

        $this->setColumnWidth('F', 20);
        $this->setCellValue('F1', 'Date');

        $this->setColumnWidth('G', 20);
        $this->setCellValue('G1', 'Amount');

        $this->setColumnWidth('H', 20);
        $this->setCellValue('H1', 'Carrier');

        if (auth()->user()->isAdmin()) {
            $this->setColumnWidth('I', 20);
            $this->setCellValue('I1', 'Carrier Cost');
        }

        $this->setColumnWidth('J', 20);
        $this->setCellValue('J1', 'Order Dimensions');

        $this->setColumnWidth('K', 20);
        $this->setCellValue('K1', 'Total Weight');

        $this->setColumnWidth('L', 20);
        $this->setCellValue('L1', 'Volumetric Weight');

        $this->setColumnWidth('M', 20);
        $this->setCellValue('M1', 'Credit/Debit');

        $this->setBackgroundColor('A1:M1', '2b5cab');
        $this->setColor('A1:M1', 'FFFFFF');

        $this->currentRow++;
    }

    private function getShippingCarrier($deposit, $order)
    {
        if ($deposit->firstOrder() && $deposit->firstOrder()->hasSecondLabel()) {
            return ($deposit->firstOrder()->us_api_service == ShippingService::UPS_GROUND) ? 'UPS' : 'USPS';
        }

        if ($order->shippingService) {
            switch ($order->recipient->country_id) {
                case ORDER::US:
                    if ($order->shippingService->sub_class_code == ShippingService::UPS_GROUND) {
                        return 'UPS';
                    }
                     return 'USPS';
                   break;
                case ORDER::CHILE:
                        return 'Correios Chile';
                    break;
                case ORDER::BRAZIL:
                        return 'Correios Brazil';
                    break;
                default:
                    return '';
                   break;
            }
        }
       return optional($order->shippingService)->name;
    }

    private function getShippingCarrierCost($deposit, $order)
    {
        if ($deposit->firstOrder() && $deposit->firstOrder()->hasSecondLabel()) {
            if ($order->us_secondary_label_cost) {
                return $order->us_secondary_label_cost['api_cost'];
            }
            return '';
        }

        if ($order->recipient->country_id == Order::BRAZIL || $order->recipient->country_id == Order::CHILE) {
            return  $this->getValuePaidToCorrieos($order);
        }

        return $order->user_declared_freight;
    }

    private function getValuePaidToCorrieos($order)
    {
        $rateSlab = AccrualRate::getRateSlabFor($order->getWeight('kg'));

        $container = $order->containers->first();

        if (!$container) {
            return $rateSlab->gru;
        }

        switch ($container->getDestinationAriport()) {
            case "GRU" || "Santiago":
                return $rateSlab->gru;

            default:
                return $rateSlab->cwb;
        }
    }

    private function getOrderDimensions($order)
    {
       return $order->length. ' X '. $order->width.' X '.$order->height;
    }

    private function getOrderTotalWeight($order)
    {
       return $order->getOriginalWeight('kg');
    }

    public function getOrderVolumetricWeight($order)
    {
        return $order->getWeight('kg');
    }
}
