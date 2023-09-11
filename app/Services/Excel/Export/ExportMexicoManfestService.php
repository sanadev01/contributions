<?php 

namespace App\Services\Excel\Export;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\ShippingService;
use App\Models\Warehouse\Container;
use App\Models\Warehouse\AccrualRate;
use App\Models\Warehouse\DeliveryBill;

class ExportMexicoManfestService extends AbstractCsvExportService
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

    protected function prepareHeaders() : array
    {
        return [
            'HAWB',
            'SACA',
            'Tracking Number (HAWB)',
            'SHIPPER',
            'SHIPPER ADDRESS',
            'SHIPPER CITY NAME',  
            'SHIPPERS CITY CODE',
            'SHIPPERS COUNTRY NAME',
            'SHIPPERS COUNTRY CODE',
            'CONSIGNEE (CNNE)',
            'CNNE ADDRESS',
            'CNNE CITY NAME',
            'CNNE ZIP CODE',
            'CNNE PH NUMBER',
            'CNEE COUNTRY CODE',
            'PARCEL Weight',
            'Weight UNIT',
            'PRODUCT DESCRIPTION',
            'TOTAL QTY OF ITEMS IN PARCELc',
            'CURRENCY',
            'TOTAL DECLARED VALUE'
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
                '',
                '',
                $package->getSenderFullName(), 
                $package->sender_address,
                $package->sender_city,
                $package->sender_city_zipcode,
                $package->senderCountry->name,
                $package->senderCountry->code, 
                ($package->recipient)->fullName(),
                ($package->recipient)->getAddress(), 
                ($package->recipient)->city,
                ($package->recipient)->zipcode,
                ($package->recipient)->phone_number,
                ($package->recipient)->country->code,
                $package->weight,
                $package->measurement_unit,
                '',
                '', 
                'USD',
                $package->gross_total,
            ];

            $i=0;
            foreach ($package->items as $item) {
                if ( $i>0 ){
                    $this->csvData[$this->row] = array_fill(0,20,'');
                }
                $this->csvData[$this->row][17] = $item->description;
                $this->csvData[$this->row][18] = $item->quantity * $item->items;
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