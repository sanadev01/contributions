<?php 

namespace App\Services\Excel\Export;

use App\Models\Order;
use App\Models\Warehouse\AccrualRate;
use Carbon\Carbon;
use App\Models\Warehouse\Container;
use App\Models\Warehouse\DeliveryBill;

class ExportManfestByServices extends AbstractCsvExportService
{
    private $deliveryBill;
    private $csvData = [];
    private $row = 0;
    private $totalCustomerPaid;
    private $totalPaidToCorreios;
    private $totalPieces = 0;
    private $totalWeight = 0;

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
            'Value of product',
            'WHR#',
            'Customer paid',
            'Airport/ GRU/CWB',
            'Value paid to Correios',
            'Bag',
            'POBOX / NAME',
            'Correios Brazil',
            'Correios Chile',
            'UPS',
            'USPS',
            'Fedex',
            'Carrier Tracking'
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
                $this->getValuePaidToCorrieos($container,$package),
                $container->dispatch_number,
                optional($package->user)->pobox_number.' / '.optional($package->user)->getFullName(),
                $package->carrierService() == 'Correios Brazil'? 'Correios Brazil': '',
                $package->carrierService() == 'Correios Chile'? 'Correios Chile': '',
                $package->carrierService() == 'USPS'? 'USPS': '',
                $package->carrierService() == 'UPS'? 'UPS': '',
                $package->carrierService() == 'FEDEX'? 'FEDEX': '',
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
            $this->totalPaidToCorreios += $this->getValuePaidToCorrieos($container,$package);
            $this->totalPieces++;
            $this->totalWeight += $package->getOriginalWeight('kg');
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
            '',
            '',
            ''
        ];

    }

    protected function getValuePaidToCorrieos(Container $container, Order $order)
    {
        $rateSlab = AccrualRate::getRateSlabFor($order->getWeight('kg'));

        if ( !$rateSlab ){
            return 0;
        }

        if ( $container->getDestinationAriport() ==  "GRU"){
            return $rateSlab->gru;
        }

        return $rateSlab->cwb;
    }
}