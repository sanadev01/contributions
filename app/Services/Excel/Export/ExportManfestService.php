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
    private $total_customerpaid;
    private $total_paid_to_correios;

    public function __construct(DeliveryBill $deliveryBill)
    {
        $this->deliveryBill = $deliveryBill;
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
            'Bag',
            'POBOX / NAME'
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
                Carbon::now()->format('m/d/Y'),
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
                $this->getValuePaidToCorrieos($container,$package)['airport'],
                $this->getValuePaidToCorrieos($container,$package)['commission'],
                $container->dispatch_number,
                optional($package->user)->pobox_number.' / '.optional($package->user)->getFullName()
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

            $this->total_customerpaid +=  $package->gross_total;
            $this->total_paid_to_correios += $this->getValuePaidToCorrieos($container,$package)['airport'];
        }

        $this->csvData[$this->row] = [
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            'Total',
            $this->total_customerpaid,
            '',
            $this->total_paid_to_correios,
            '',
            '',
            '',
        ];

    }

    protected function getValuePaidToCorrieos(Container $container, Order $order)
    {
        $commission = false;
        $rateSlab = AccrualRate::getRateSlabFor($order->getWeight('kg'));

        if ( !$rateSlab ){
            return [
                'airport'=> 0,
                'commission'=> 0
            ];
        }
        $service = $order->shippingService->service_sub_class;
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