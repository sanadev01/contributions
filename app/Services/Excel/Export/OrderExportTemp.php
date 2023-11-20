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
         } elseif($order->shippingService->service_sub_class == ShippingService::LT_PRIME) {
            $type = 'Priority';
         }  
            $user = $order->user; 
            $this->setCellValue('A'.$row, $order->containers->first()->awb);
            $this->setCellValue('B'.$row, $order->containers->first()->seal_no );
            $this->setCellValue('C'.$row, (string)$this->getOrderTrackingCodes($order));
            $this->setCellValue('D'.$row, optional($order->recipient)->getFullName());
            $this->setCellValue('E'.$row, optional($order->recipient)->zipcode);
            $this->setCellValue('F'.$row, optional(optional($order->recipient)->state)->name);
            $this->setCellValue('G'.$row, optional($order->recipient)->city);
            $this->setCellValue('H'.$row, optional($order->recipient)->address.' '.optional($order->recipient)->street_no);
            $this->setCellValue('I'.$row, optional($order->recipient)->phone.' ');
            $this->setCellValue('J'.$row, optional($order->recipient)->phone.' ');
            $this->setCellValue('K'.$row, optional($order->recipient)->email);
            $this->setCellValue('L'.$row, optional(optional($order->recipient)->country)->code);
            $this->setCellValue('M'.$row, '');
            $this->setCellValue('N'.$row, $order->items->first()->description);
            $this->setCellValue('O'.$row, $order->items->first()->sh_code);
            $this->setCellValue('P'.$row, $order->items->count());
            $this->setCellValue('Q'.$row, $order->getOriginalWeight()/$order->items->count());
            $this->setCellValue('R'.$row, $order->getOriginalWeight());
            $this->setCellValue('S'.$row, $order->items->sum('value')/$order->items->count());
            $this->setCellValue('T'.$row, $order->items->sum('value'));
            $this->setCellValue('U'.$row, 'USD');
            $this->setCellValue('V'.$row, $type);
            $this->setCellValue('W'.$row, $order->containers->first()->tax_modality);
            $this->setCellValue('x'.$row, $order->recipient->tax_id);
            $this->setCellValue('Y'.$row, '');   
            $this->setCellValue('Z'.$row,'');
            $this->setCellValue('AA'.$row,'B2C');
            $this->setCellValue('AB'.$row, $type == 'Priority' ? 'LTPO' : 'UZPO');
            $this->setCellValue('AC'.$row, $order->carrierService());
            $this->setCellValue('AD'.$row, $type == 'Priority' ? 'LTPO '.$type : 'UZPO '.$type);
            $this->setCellValue('AE'.$row,'');
            $this->setCellValue('AF'.$row,'');
            $this->setCellValue('AG'.$row,'');
            $this->setCellValue('AH'.$row,'');
            $this->setCellValue('AI'.$row,$order->status==Order::STATUS_CANCEL?'TRUE':'FALSE');
            
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
        $this->setCellValue('C1', 'PARCEL ID');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'NAME');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'ZIP');

        $this->setColumnWidth('F', 23);
        $this->setCellValue('F1', '	REGION');

        $this->setColumnWidth('G', 25);
        $this->setCellValue('G1', 'CITY');

        $this->setColumnWidth('H', 25);
        $this->setCellValue('H1', 'ADDRESS');
        
        $this->setColumnWidth('I', 25);
        $this->setCellValue('I1', 'PHONE NUMBER ');
        
        $this->setColumnWidth('J', 25);
        $this->setCellValue('J1', 'PHONE NORMALIZED');

        $this->setColumnWidth('K', 20);
        $this->setCellValue('K1', 'EMAIL');
        
        $this->setColumnWidth('L', 20);
        $this->setCellValue('L1', 'COUNTRY');
 
        $this->setColumnWidth('M', 20);
        $this->setCellValue('M1', 'SKU CODE');

        $this->setColumnWidth('N', 20); 
        $this->setCellValue('N1', 'DESCRIPTION OF CONTENT');

        $this->setColumnWidth('O', 20);
        $this->setCellValue('O1', 'HS CODE');

        $this->setColumnWidth('P', 20);
        $this->setCellValue('P1', 'QUANTITY');

        $this->setColumnWidth('Q', 20);
        $this->setCellValue('Q1', 'WEIGHT PER ITEM,KG');

        $this->setColumnWidth('R', 20);
        $this->setCellValue('R1', 'WEIGHT PER PARCEL,KG');
        
        $this->setColumnWidth('S', 20);
        $this->setCellValue('S1', 'PRICE PER ITEM');
 
        $this->setColumnWidth('T', 20);
        $this->setCellValue('T1', 'PRICE PER PARCEL');

        $this->setColumnWidth('U', 20);
        $this->setCellValue('U1', 'CURRENCY');

        $this->setColumnWidth('V', 20);
        $this->setCellValue('V1', 'MAIL TYPE');

        $this->setColumnWidth('W', 20);
        $this->setCellValue('W1', 'TAX TYPE');

        $this->setColumnWidth('X', 20);
        $this->setCellValue('X1', 'TAX IDENTIFICATION');

        $this->setColumnWidth('Y', 20);
        $this->setCellValue('Y1', 'ROUTE INFO');

        $this->setColumnWidth('Z', 20);
        $this->setCellValue('Z1', 'SHIP DATE');

        $this->setColumnWidth('AA', 20);
        $this->setCellValue('AA1', 'TRANSACTION TYPE'); 
        
        $this->setColumnWidth('AB', 20);
        $this->setCellValue('AB1', 'SERVICE CODE'); 
        
        $this->setColumnWidth('AC', 20);
        $this->setCellValue('AC1', 'CARRIER SERVICE CODE'); 
        
        $this->setColumnWidth('AD', 20);
        $this->setCellValue('AD1', 'CARRIER'); 
        
        $this->setColumnWidth('AE', 20);
        $this->setCellValue('AE1', 'MANIFEST PARCEL NR'); 
        
        $this->setColumnWidth('AF', 20);
        $this->setCellValue('AF1', 'EXTERNAL ID'); 
        
        $this->setColumnWidth('AG', 20);
        $this->setCellValue('AG1', 'WARNING'); 
        
        $this->setColumnWidth('AH', 20);
        $this->setCellValue('AH1', 'ERRORS'); 
        
        $this->setColumnWidth('AI', 20);
        $this->setCellValue('AI1', 'CANCELLED'); 

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
