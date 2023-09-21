<?php 

namespace App\Services\CorreosChile;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Warehouse\Container;
use App\Models\Warehouse\AccrualRate;
use App\Models\Warehouse\DeliveryBill;
use App\Services\Excel\Export\AbstractCsvExportService;

class ExportExcelChileManifestService extends AbstractCsvExportService
{
    private $container;
    private $csvData = [];
    private $row = 0;
    private $total_customerpaid;
    private $total_paid_to_correios;

    public function __construct($container)
    {
        $this->container = $container;
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
            'Contents',
            'NCM',
            'Value',
            'WHR#',
            'Customer paid',
            'Airport/ GRU/CWB',
            'Value paid to Correios',
            'Bag',
            'POBOX / NAME'
        ];
    }

    protected function prepareData(): array
    {
        
        $this->prePareDataForContainer($this->container);
        return $this->csvData;
    }

    protected function prePareDataForContainer($container)
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
                7 => 'contents',
                8 => 'ncm',
                $package->getOrderValue(),
                $package->warehouse_number,
                $package->gross_total,
                $container->getDestinationAriport(),
                $this->getValuePaidToCorrieos($container,$package),
                $container->dispatch_number,
                optional($package->user)->pobox_number.' / '.optional($package->user)->getFullName()
            ];

            $i=0;
            foreach ($package->items as $item) {
                if ( $i>0 ){
                    $this->csvData[$this->row] = array_fill(0,14,'');
                }

                $this->csvData[$this->row][7] = $item->description;
                $this->csvData[$this->row][8] = $item->sh_code;

                $this->row++;
                
                $i++;
            }

            $this->row++;

            $this->total_customerpaid +=  $package->gross_total;
            $this->total_paid_to_correios += $this->getValuePaidToCorrieos($container,$package);
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
        ];

    }

    protected function getValuePaidToCorrieos(Container $container, Order $order)
    {
        $service  = $order->shippingService->service_sub_class;
        $rateSlab = AccrualRate::getRateSlabFor($order->getWeight('kg'),$service);

        if ( !$rateSlab ){
            return 0;
        }

        if ( $container->getDestinationAriport() ==  "Santiago"){
            return $rateSlab->gru;
        }

        return $rateSlab->cwb;
    }
}