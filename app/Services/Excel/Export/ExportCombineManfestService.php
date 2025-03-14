<?php

namespace App\Services\Excel\Export;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\ShippingService;
use App\Models\Warehouse\Container;
use App\Models\Warehouse\AccrualRate;
use App\Services\Excel\Export\AbstractCsvExportService;

class ExportCombineManfestService extends AbstractCsvExportService
{
    private $deliveryBills;
    private $csvData = [];
    private $row = 0;
    private $totalCustomerPaid;
    private $totalPaidToCorreios;
    private $totalPieces = 0;
    private $totalWeight = 0;
    private $totalCommission = 0;
    private $totalAnjunCommission = 0;
    private $date;

    public function __construct($deliveryBills)
    {
        $this->deliveryBills = $deliveryBills;
    }

    public function handle()
    {
        return $this->download();
    }

    protected function prepareHeaders() : array
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
            'Value',
            'WHR#',
            'Customer paid',
            'Airport/ GRU/CWB',
            'Value paid to Correios',
            'Commission paid to Anjun',
            'Referrer Commission',
            'Commission Paid to',
            'Bag',
            'POBOX / NAME',
            'Carrier Tracking'
        ];
    }

    protected function prepareData(): array
    {
        foreach ($this->deliveryBills as $deliveryBill) {
            $this->date = $deliveryBill->created_at->format('m/d/Y');
            foreach ($deliveryBill->containers as $container) {
                $this->prePareDataForContainer($container);
            }
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
            ''
        ];
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
                $package->getOrderValue(),
                $package->warehouse_number,
                $package->gross_total,
                $container->getDestinationAriport(),
                $this->getValuePaidToCorrieos($container, $package)['airport'],
                $this->getValuePaidToCorrieos($container, $package)['commission'],
                optional($package->affiliateSale)->commission,
                optional(optional($package->affiliateSale)->user)->pobox_number  .' '.optional(optional($package->affiliateSale)->user)->name,
                $container->dispatch_number,
                optional($package->user)->pobox_number.' / '.optional($package->user)->getFullName(),
                $package->tracking_id
            ];

            $i=0;
            foreach ($package->items as $item) {
                if ( $i>0 ){
                    $this->csvData[$this->row] = array_fill(0,14,'');
                }

                $this->csvData[$this->row][8] = $item->description;
                $this->csvData[$this->row][9] = $item->sh_code;

                $this->row++;
                
                $i++;
            }

            $this->row++;

            $this->totalCustomerPaid +=  $package->gross_total;
            $this->totalPaidToCorreios += $this->getValuePaidToCorrieos($container,$package)['airport'];
            $this->totalPieces++;
            $this->totalWeight += $package->getOriginalWeight('kg');
            $this->totalCommission += optional($package->affiliateSale)->commission;
            $this->totalAnjunCommission += $this->getValuePaidToCorrieos($container,$package)['commission'];
        }
    }

    protected function getValuePaidToCorrieos(Container $container, Order $order)
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
        if ( $container->getDestinationAriport() ==  "GRU"){
            return [
                'airport'=> $rateSlab->gru,
                'commission'=> $commission ? $rateSlab->commission : 0
            ];
        }
        return [
            'airport'=> $rateSlab->cwb,
            'commission'=> $commission ? $rateSlab->commission : 0
        ];
    }
    
}