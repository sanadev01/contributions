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

            //$order = $deposit->getOrder($deposit->order_id);
            $order = ($deposit->orders) ? $deposit->orders->first() : null;
            // $depositFirstOrder = $deposit->firstOrder();
            $depositFirstOrder = ($order) ? $order : null;
            if ($order == null && !$deposit->is_credit) {
                $order = $deposit->getOrder($deposit->order_id);
            }
            if (!$order) {
                continue;
            }
            $this->setCellValue('A'.$row, $deposit->uuid);
            $this->setCellValue('B'.$row, optional($order)->warehouse_number);
            $this->setCellValue('C'.$row, optional(optional($order)->recipient)->fullName());
            $this->setCellValue('D'.$row, optional($order)->customer_reference);
            $this->setCellValue('E'.$row, ($depositFirstOrder && $depositFirstOrder->has_second_label) ? optional($depositFirstOrder)->us_api_tracking_code : optional($order)->corrios_tracking_code);
            $this->setCellValue('F'.$row, $deposit->created_at->format('m/d/Y'));
            $this->setCellValue('G'.$row, $deposit->amount);
            $this->setCellValue('H'.$row, $order->tax_and_duty);
            $this->setCellValue('I'.$row, $order->fee_for_tax_and_duty);

            $this->setCellValue('J'.$row, $this->getShippingCarrier($depositFirstOrder, $order));
            if (auth()->user()->isAdmin()) {
                $this->setCellValue('K'.$row, '');
            }
            $this->setCellValue('L'.$row, $order ? $order->length.'x'.$order->width.'x'.$order->height : '');
            $this->setCellValue('M'.$row, $order ? $order->weight : '');
            $this->setCellValue('N'.$row, '');
            $this->setCellValue('O'.$row, $deposit->isCredit() ? 'Credit' : 'Debit');
            $this->setCellValue('P'.$row, intval($deposit->last_four_digits) && strlen($deposit->last_four_digits)==4  ? 'Card':$deposit->last_four_digits );
            $this->setCellValue('Q'.$row, $deposit->description);
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
        $this->setCellValue('H1', 'T&D Taxes');

        $this->setColumnWidth('I', 20);
        $this->setCellValue('I1', 'Convenience Fee');


        $this->setColumnWidth('J', 20);
        $this->setCellValue('J1', 'Carrier');

        if (auth()->user()->isAdmin()) {
            $this->setColumnWidth('K', 20);
            $this->setCellValue('K1', 'Carrier Cost');
        }

        $this->setColumnWidth('L', 20);
        $this->setCellValue('L1', 'Order Dimensions');

        $this->setColumnWidth('M', 20);
        $this->setCellValue('M1', 'Total Weight');

        $this->setColumnWidth('N', 20);
        $this->setCellValue('N1', 'Volumetric Weight');

        $this->setColumnWidth('O', 20);
        $this->setCellValue('O1', 'Credit/Debit');


        $this->setColumnWidth('P', 30);
        $this->setCellValue('P1', 'Type');

        $this->setColumnWidth('Q', 30);
        $this->setCellValue('Q1', 'Description');

        $this->setBackgroundColor('A1:Q1', '2b5cab');
        $this->setColor('A1:Q1', 'FFFFFF');

        $this->currentRow++;
    }

    private function getShippingCarrier($depositFirstOrder, $order)
    {
        if ($depositFirstOrder && $depositFirstOrder->has_second_label) {
            switch ($depositFirstOrder->us_api_service) {
                case ShippingService::UPS_GROUND:
                    return 'UPS';
                    break;
                case ShippingService::FEDEX_GROUND:
                    return 'FedEx';
                    break;
                default:
                    return 'USPS';
                    break;
            }
        }

        if (optional($order)->shippingService) {
            switch ($order->recipient->country_id) {
                case ORDER::US:
                    if ($order->shippingService->sub_class_code == ShippingService::UPS_GROUND) {
                        return 'UPS';
                    }
                    if ($order->shippingService->sub_class_code == ShippingService::FEDEX_GROUND) {
                        return 'FedEx';
                    }
                    return 'USPS';
                    break;
                case ORDER::CHILE:
                    return 'Correios Chile';
                    break;
                case ORDER::BRAZIL:
                    if ($order->shippingService->sub_class_code == ShippingService::GePS || $order->shippingService->sub_class_code == ShippingService::GePS_EFormat || $order->shippingService->sub_class_code == ShippingService::Parcel_Post) {
                        return 'Global eParcel';
                    }
                    if ($order->shippingService->sub_class_code == ShippingService::Prime5 || $order->shippingService->sub_class_code == ShippingService::Prime5RIO) {
                        return 'Prime5';
                    }
                    if (in_array(
                        $order->shippingService->sub_class_code,
                        [
                            ShippingService::Packet_Standard,
                            ShippingService::Packet_Express,
                            ShippingService::AJ_Packet_Standard,
                            ShippingService::AJ_Packet_Express,
                            ShippingService::BCN_Packet_Standard,
                            ShippingService::BCN_Packet_Express
                        ]
                    )) {
                        return 'Correios Brazil';
                    }
                    break;
                default:
                    return ' ';
                    break;
            }
        }
        return optional(optional($order)->shippingService)->name;
    }

    private function getShippingCarrierCost($depositFirstOrder, $order)
    {
        if ($depositFirstOrder && $depositFirstOrder->has_second_label) {
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
        $service  = $order->shippingService->service_sub_class;
        $rateSlab = AccrualRate::getRateSlabFor($order->getWeight('kg'), $service);

        $container = $order->containers->first();

        if (!$container) {
            return $rateSlab->gru;
        }

        switch ($container->destination_ariport) {
            case "GRU" || "Santiago":
                return $rateSlab->gru;

            default:
                return $rateSlab->cwb;
        }
    }

    private function getOrderDimensions($order)
    {
        return $order->length . ' X ' . $order->width . ' X ' . $order->height;
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
