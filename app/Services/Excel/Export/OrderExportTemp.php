<?php

namespace App\Services\Excel\Export;
use App\Models\User;
use App\Models\Order;
use App\Models\ShippingService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class OrderExportTemp extends AbstractExportService
{
    private $orders;
    private $user;
    private $id;

    private $currentRow = 1;

    public function __construct(Collection $orders, $id)
    {
        $this->orders = $orders;
        $this->id = $id;
        $this->authUser = User::find($this->id);

        parent::__construct();
    }

    public function handle()
    {
        $this->prepareExcelSheet();

        return $this->downloadExcel();
    }

    private function prepareExcelSheet()
    {
        $this->setExcelHeaderRow();

        $row = $this->currentRow;


        foreach ($this->orders as $order) {
            
        if($order->shippingService->service_sub_class == ShippingService::Post_Plus_Registered) {
            $type = 'Registered';
         } elseif($order->shippingService->service_sub_class == ShippingService::Post_Plus_EMS) {
            $type = 'EMS';
         } elseif($order->shippingService->service_sub_class == ShippingService::Post_Plus_Prime) {
            $type = 'Prime';
         } elseif($order->shippingService->service_sub_class == ShippingService::Post_Plus_Premium) {
            $type = 'ParcelUPU';
         }
            $user = $order->user; 
            $this->setCellValue('A'.$row, $order->containers->first()->awb);
            $this->setCellValue('B'.$row, $order->warehouse_number );
            $this->setCellValue('C'.$row, (string)$this->getOrderTrackingCodes($order));
            $this->setCellValue('D'.$row, $order->customer_reference);
            $this->setCellValue('E'.$row, $user->name);
            $this->setCellValue('F'.$row, $order->recipient->zipcode);
            $this->setCellValue('G'.$row, $order->recipient->state->name);
            $this->setCellValue('H'.$row, $order->recipient->city);
            $this->setCellValue('I'.$row, $order->recipient->address);
            $this->setCellValue('J'.$row, $order->recipient->street_no);
            $this->setCellValue('K'.$row, $order->recipient->phone);
            $this->setCellValue('L'.$row, $order->recipient->phone);
            $this->setCellValue('M'.$row, $order->recipient->email);
            $this->setCellValue('N'.$row, $order->recipient->country->name);
            $this->setCellValue('O'.$row, '');
            $this->setCellValue('P'.$row, $order->items->first()->description);
            $this->setCellValue('Q'.$row, $order->items->first()->sh_code);
            $this->setCellValue('R'.$row, $order->items->count());
            $this->setCellValue('S'.$row, $order->getOriginalWeight()/$order->items->count());
            $this->setCellValue('T'.$row, $order->getOriginalWeight());
            $this->setCellValue('U'.$row, $order->items->sum('value')/$order->items->count());
            $this->setCellValue('V'.$row, $order->items->sum('value'));
            $this->setCellValue('W'.$row, 'USD');
            $this->setCellValue('x'.$row, $type);
            $this->setCellValue('Y'.$row, $order->containers->first()->tax_modality);
            $this->setCellValue('Z'.$row, $order->recipient->tax_id);
            $this->setCellValue('AA'.$row, '');   
            $this->setCellValue('AB'.$row,'');
            $this->setCellValue('AC'.$row,'B2C');
            $this->setCellValue('AD'.$row, 'UZPO');
            $this->setCellValue('AE'.$row, $order->carrierService());
            $this->setCellValue('AF'.$row, 'UZPO-'.$type);
            $this->setCellValue('AG'.$row,'');
            $this->setCellValue('AH'.$row,'');
            $this->setCellValue('AI'.$row,'');
            $this->setCellValue('AJ'.$row,'');
            $this->setCellValue('AK'.$row,$order->status==Order::STATUS_CANCEL?'TRUE':'FALSE'); 
            

            
            $row++;
        }


    }

    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'WAY BILL');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'PACKAGE ID');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'PERCEL ID');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'CLINET ID');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'NAME');

        $this->setColumnWidth('F', 20);
        $this->setCellValue('F1', 'ZIP');

        $this->setColumnWidth('G', 23);
        $this->setCellValue('G1', '	REGION');

        $this->setColumnWidth('H', 25);
        $this->setCellValue('H1', 'CITY');

        $this->setColumnWidth('I', 25);
        $this->setCellValue('I1', 'ADDRESS');
        
        $this->setColumnWidth('J', 25);
        $this->setCellValue('J1', 'House Number');
        
        $this->setColumnWidth('K', 25);
        $this->setCellValue('K1', 'PHONE NUMBER');

        $this->setColumnWidth('L', 20);
        $this->setCellValue('L1', 'PHONE NORMALIZED');
        
        $this->setColumnWidth('M', 20);
        $this->setCellValue('M1', 'EMAIL');
 
        
        $this->setColumnWidth('N', 20);
        $this->setCellValue('N1', 'COUNTRY');

        $this->setColumnWidth('O', 20); 
        $this->setCellValue('O1', 'SKU CODE');

        $this->setColumnWidth('P', 20);
        $this->setCellValue('P1', 'DESCRIPTION OF CONTENT');

        $this->setColumnWidth('Q', 20);
        $this->setCellValue('Q1', 'HS CODE');

        $this->setColumnWidth('R', 20);
        $this->setCellValue('R1', 'QUANTITY');

        $this->setColumnWidth('S', 20);
        $this->setCellValue('S1', 'WEIGHT PER ITEM,KG');
        
        $this->setColumnWidth('T', 20);
        $this->setCellValue('T1', 'WEIGHT PER PARCEL,KG');
 
        $this->setColumnWidth('U', 20);
        $this->setCellValue('U1', 'PRICE PER ITEM');

        $this->setColumnWidth('V', 20);
        $this->setCellValue('V1', 'PRICE PER PARCEL');

        $this->setColumnWidth('W', 20);
        $this->setCellValue('W1', 'CURRENCY');
        $this->setColumnWidth('X', 20);
        $this->setCellValue('X1', 'MAIL TYPE');
        $this->setColumnWidth('Y', 20);
        $this->setCellValue('Y1', 'TAX TYPE');
        $this->setColumnWidth('Z', 20);
        $this->setCellValue('Z1', 'TAX IDENTIFICATION');
        $this->setColumnWidth('AA', 20);
        $this->setCellValue('AA1', 'ROUTE INFO'); 
        $this->setColumnWidth('AB', 20);
        $this->setCellValue('AB1', 'SHIP DATE'); 
        $this->setColumnWidth('AC', 20);
        $this->setCellValue('AC1', 'TRANSACTION TYPE'); 
        $this->setColumnWidth('AD', 20);
        $this->setCellValue('AD1', 'SERVICE CODE'); 
        $this->setColumnWidth('AE', 20);
        $this->setCellValue('AE1', 'CARRIER SERVICE CODE'); 
        $this->setColumnWidth('AF', 20);
        $this->setCellValue('AF1', 'CARRIER'); 
        $this->setColumnWidth('AG', 20);
        $this->setCellValue('AG1', 'MANIFEST PARCEL NR'); 
        $this->setColumnWidth('AH', 20);
        $this->setCellValue('AH1', 'EXTERNAL ID'); 
        $this->setColumnWidth('AI', 20);
        $this->setCellValue('AI1', 'WARNING'); 
        $this->setColumnWidth('AJ', 20);
        $this->setCellValue('AJ1', 'ERRORS');
        $this->setColumnWidth('AK', 20);
        $this->setCellValue('AK1', 'CANCELLED');
        $this->setBackgroundColor('A1:AK1', '2b5cab');
        $this->setColor('A1:AK1', 'FFFFFF');

        $this->currentRow++;
    }

 

    public function isWeightInKg($measurement_unit)
    {
        return $measurement_unit == 'kg/cm' ? 'kg' : 'lbs';
    }

    public function chargeWeight($order)
    {
        $getOriginalWeight = $order->getOriginalWeight('kg');
        $chargeWeight = $getOriginalWeight;
        $getWeight = $order->getWeight('kg');
        if($getWeight > $getOriginalWeight && $order->weight_discount){
            $discountWeight = $order->weight_discount;
            if($order->measurement_unit == 'lbs/in'){
                $discountWeight = $order->weight_discount/2.205;
            }
            $consideredWeight = $getWeight - $getOriginalWeight;
            $chargeWeight = ($consideredWeight - $discountWeight) + $getOriginalWeight;
        }
        
        return round($chargeWeight,2);
    }

    private function getOrderTrackingCodes($order)
    {
        $trackingCodes = ($order->hasSecondLabel() ? $order->corrios_tracking_code.','.$order->us_api_tracking_code : $order->corrios_tracking_code);
        return (string)$trackingCodes;
    }
     
}
