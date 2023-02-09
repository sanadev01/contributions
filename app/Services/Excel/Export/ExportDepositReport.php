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
            
            $depositOrder = $deposit->order;            
            $order = $deposit->orders ? $deposit->orders->first() : null;
            $depositFirstOrder = $order?? null;

            $this->setCellValue('A'.$row, $deposit->uuid);
            $this->setCellValue('B'.$row, $deposit->order_warehouse_no);
            $this->setCellValue('C'.$row, $deposit->order_recipient_name);
            $this->setCellValue('D'.$row, $deposit->order_customer_reference);
            $this->setCellValue('E'.$row, $deposit->order_tracking_code);
            $this->setCellValue('F'.$row, $deposit->created_at->format('m/d/Y'));
            $this->setCellValue('G'.$row, $deposit->amount);
            $this->setCellValue('H'.$row, $this->getShippingCarrier($depositFirstOrder, $depositOrder));
            if (auth()->user()->isAdmin()) {
                $this->setCellValue('I'.$row, '');
            }
            $this->setCellValue('J'.$row, $deposit->order_dimensions);
            $this->setCellValue('K'.$row, $deposit->order_weight);
            $this->setCellValue('L'.$row, '');
            $this->setCellValue('M'.$row, $deposit->isCredit() ? 'Credit' : 'Debit');
            $this->setCellValue('N'.$row, $deposit->description);
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

        $this->setColumnWidth('N', 20);
        $this->setCellValue('N1', 'Description');

        $this->setBackgroundColor('A1:N1', '2b5cab');
        $this->setColor('A1:N1', 'FFFFFF');

        $this->currentRow++;
    }

    private function getShippingCarrier($depositFirstOrder, $depositOrder)
    {
        if ($depositFirstOrder && $depositFirstOrder->hasSecondLabel()) {
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

        if (optional($depositOrder)->shippingService) {
            switch ($depositOrder->recipient->country_id) {
                case ORDER::US:
                    if ($depositOrder->shippingService->sub_class_code == ShippingService::UPS_GROUND) {
                        return 'UPS';
                    }
                    if ($depositOrder->shippingService->sub_class_code == ShippingService::FEDEX_GROUND) {
                        return 'FedEx';
                    }
                     return 'USPS';
                   break;
                case ORDER::CHILE:
                        return 'Correios Chile';
                    break;
                case ORDER::BRAZIL:
                    if ($depositOrder->shippingService->sub_class_code == ShippingService::GePS || $depositOrder->shippingService->sub_class_code == ShippingService::GePS_EFormat) {
                        return 'Global eParcel';
                    }
                    if ($depositOrder->shippingService->sub_class_code == ShippingService::Prime5) {
                        return 'Prime5';
                    }
                    if (in_array($depositOrder->shippingService->sub_class_code, 
                        [ShippingService::Packet_Standard, 
                        ShippingService::Packet_Express, 
                        ShippingService::AJ_Packet_Standard, 
                        ShippingService::AJ_Packet_Express])) 
                        {
                        return 'Correios Brazil';
                    }
                    break;
                default:
                    return ' ';
                   break;
            }
        }
       return optional(optional($depositOrder)->shippingService)->name;
    }

    private function getShippingCarrierCost($depositFirstOrder, $order)
    {
        if ($depositFirstOrder && $depositFirstOrder->hasSecondLabel()) {
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
        $rateSlab = AccrualRate::getRateSlabFor($order->getWeight('kg'),$service);

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
