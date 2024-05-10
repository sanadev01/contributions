<?php

namespace App\Services\Excel\Export;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\ShippingService;
use App\Models\Warehouse\Container;
use App\Models\Warehouse\AccrualRate;
use App\Models\Warehouse\DeliveryBill;

class ExportManfestService extends AbstractCsvExportService
{
    private $deliveryBill;
    private $csvData = [];
    private $row = 0;
    private $totalCustomerPaid;
    private $totalPaidToCorreios;
    private $totalPieces = 0;
    private $totalWeight = 0;
    private $totalCommission = 0;
    private $totalAnjunCommission = 0;
    private $date;

    public function __construct(DeliveryBill $deliveryBill)
    {
        $this->deliveryBill = $deliveryBill;
        $this->date = $deliveryBill->created_at->format('m/d/Y');
    }

    public function handle()
    {
        return $this->download();
    }

    protected function prepareHeaders(): array
    {
        return [
            'HAWB',
            'Date',
            'Shipper Name',
            'ConsigneeName/CPF',
            'ConsigneeAddres',
            'Piece',
            'Weigth',
            'Volumetric Weigth',
            'Contents',
            'NCM',
            'Value of product',
            'WHR#',
            'Customer paid',
            'Airport/ GRU/CWB',
            'Value paid to Correios',
            'Commission paid to Anjun',
            'Referrer Commission',
            'Commission Paid to',
            'Bag',
            'POBOX / NAME',
            'Carrier Tracking',
            'Marketplace'
        ];
    }

    protected function prepareData(): array
    {
        foreach ($this->deliveryBill->containers as $container) {
            $this->prePareDataForContainer($container);
        }

        return $this->csvData;
    }

    protected function prePareDataForContainer(Container $container)
    {
        foreach ($container->orders as $package) {
            $this->csvData[$this->row] = [
                $package->corrios_tracking_code,
                $this->date,
                $package->getSenderFullName(),
                ($package->recipient)->getRecipientInfo(),
                ($package->recipient)->getAddress(),
                1,
                $package->getOriginalWeight('kg'),
                $package->getWeight('kg'),
                8 => 'contents',
                9 => 'ncm',
                $package->order_items_value,
                $package->warehouse_number,
                $package->gross_total,
                $container->destination_ariport,
                $this->getValuePaidToCorrieos($container, $package)['airport'],
                $this->getValuePaidToCorrieos($container, $package)['commission'],
                optional($package->affiliateSale)->commission,
                optional(optional($package->affiliateSale)->user)->pobox_number  . ' ' . optional(optional($package->affiliateSale)->user)->name,
                $container->dispatch_number,
                optional($package->user)->pobox_number . ' / ' . optional($package->user)->getFullName(),
                $package->tracking_id,
                setting('marketplace_checked', null, $package->user->id) ?  setting('marketplace', null, $package->user->id) : ''
            ];

            $i = 0;
            foreach ($package->items as $item) {
                if ($i > 0) {
                    $this->csvData[$this->row] = array_fill(0, 14, '');
                }

                $this->csvData[$this->row][8] = $item->description;
                $this->csvData[$this->row][9] = $item->sh_code;

                $this->row++;

                $i++;
            }

            $this->row++;

            $this->totalCustomerPaid +=  $package->gross_total;
            $this->totalPaidToCorreios += $this->getValuePaidToCorrieos($container, $package)['airport'];
            $this->totalPieces++;
            $this->totalWeight += $package->getOriginalWeight('kg');
            $this->totalCommission += optional($package->affiliateSale)->commission;
            $this->totalAnjunCommission += $this->getValuePaidToCorrieos($container, $package)['commission'];
        }

        $this->csvData[$this->row] = [
            '',
            '',
            '',
            '',
            'Total',
            $this->totalPieces,
            $this->totalWeight,
            '',
            '',
            '',
            '',
            '',
            $this->totalCustomerPaid,
            '',
            $this->totalPaidToCorreios,
            $this->totalAnjunCommission,
            $this->totalCommission,
            '',
            '',
            '',
            ''
        ];
    }

    protected function getValuePaidToCorrieos(Container $container, Order $order)
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
        if ($container->destination_ariport ==  "GRU") {
            return [
                'airport' => $rateSlab->gru,
                'commission' => $commission ? $rateSlab->commission : 0
            ];
        }
        return [
            'airport' => $rateSlab->cwb,
            'commission' => $commission ? $rateSlab->commission : 0
        ];
    }
}
