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
            'RFC COGSIGNEE',
            'CURP COGSIGNEE',
            'PARCEL Weight',
            'Weight UNIT',
            'PRODUCT DESCRIPTION',
            'PRODUCT ORIGIN',
            'FOOTWEAR',
            'TOTAL QTY OF ITEMS IN PARCEL',
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
            $tax= $package->recipient->tax_id;
            $taxLenght =strlen($tax);
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
                $taxLenght<16? $tax:'',
                $taxLenght>16? $tax:'',
                ($package->recipient)->country->code,
                $package->weight,
                $package->measurement_unit,
                '',
                '',
                '',
                count($package->items), 
                'USD',
                $package->gross_total,
            ];

            $i=0;
            foreach ($package->items as $item) {
                if ( $i>0 ){
                    $this->csvData[$this->row] = array_fill(0,20,'');
                }
                $this->csvData[$this->row][17] = $item->description;
                $this->csvData[$this->row][18] = $item->made_in;
                $shCode = $item->shCodeModel();
                $this->csvData[$this->row][19] = $shCode->is_foot_wear?'Yes':'No';
                $this->row++;
                $i++;
            }
            $this->row++;
            $this->totalCustomerPaid +=  $package->gross_total;
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
            '',
            '',
            '',
            'Total',
            $this->totalCustomerPaid,
        ];

    }
     
}