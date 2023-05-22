<?php

namespace App\Services\Excel\Export;
use App\Models\Order;
use App\Models\ShippingService;
use App\Models\Warehouse\AccrualRate;
use App\Models\Warehouse\Container;
use App\Models\Warehouse\DeliveryBill;

class ExportUsCustomerLabelManifest extends AbstractExportService
{
    private $currentRow = 1;
    private $deliveryBill;
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
        $this->date = $deliveryBill->created_at->format('m/d/Y"');
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
        foreach ($this->deliveryBill->containers as $container){
        foreach ($container->orders as $package) {
                 $this->setCellValue('A'.$row, $this->deliveryBill->name);
                 $this->setCellValue('B'.$row, $container->awb);
                 $this->setCellValue('C'.$row, $container->awb);
                 $this->setCellValue('D'.$row, $container->awb);  
            }
            $row++;
        } 
        $this->currentRow = $row; 
    }
    private function setExcelHeaderRow()
    {
 
 
        $this->setColumnValueAndWidth('A',$this->currentRow, "Waybill Typ"); 
        $this->setColumnValueAndWidth('B',$this->currentRow, "AWB Prefix"); 
        $this->setColumnValueAndWidth('C',$this->currentRow, "AWB Number"); 
        $this->setColumnValueAndWidth('D',$this->currentRow, "HAWB Number"); 
        $this->setColumnValueAndWidth('E',$this->currentRow, "Airport Of Origin");
        $this->setColumnValueAndWidth('F',$this->currentRow, "Permit to Proceed Destination Airport");
        $this->setColumnValueAndWidth('G',$this->currentRow, "Date of Arrival at the Permit to Proceed Destination Airport");
        $this->setColumnValueAndWidth('H',$this->currentRow, "Airport Of Arrival");
        $this->setColumnValueAndWidth('I',$this->currentRow, "Cargo Terminal Operator");
        $this->setColumnValueAndWidth('J',$this->currentRow, "Importing Carrier Air Carrier Code");
        $this->setColumnValueAndWidth('K',$this->currentRow, "Flight Number");
        $this->setColumnValueAndWidth('L',$this->currentRow, "Scheduled Date Of Arrival");
        $this->setColumnValueAndWidth('M',$this->currentRow, "Boarded Quantity Identifier");
        $this->setColumnValueAndWidth('N',$this->currentRow, "Boarded Pieces");
        $this->setColumnValueAndWidth('O',$this->currentRow, "Boarded Weight");
        $this->setColumnValueAndWidth('P',$this->currentRow, "Boarded Weight Code");
        $this->setColumnValueAndWidth('Q',$this->currentRow, "Part Arrival Reference");
        $this->setColumnValueAndWidth('R',$this->currentRow, "Manufacturer Registration Number");
        $this->setColumnValueAndWidth('S',$this->currentRow, "Shipper Name");
        $this->setColumnValueAndWidth('T',$this->currentRow, "Shipper Street Address");
        $this->setColumnValueAndWidth('U',$this->currentRow, "Shipper City");
        $this->setColumnValueAndWidth('V',$this->currentRow, "Shipper State or Province");
        $this->setColumnValueAndWidth('W',$this->currentRow, "Shipper Postal Code");
        $this->setColumnValueAndWidth('X',$this->currentRow, "Shipper Country");
        $this->setColumnValueAndWidth('Y',$this->currentRow, "Consignee Name");
        $this->setColumnValueAndWidth('Z',$this->currentRow, "Consignee Identifier Code");
 
       
        $this->setColumnValueAndWidth('AA',$this->currentRow,"Consignee Tax ID");
        $this->setColumnValueAndWidth('AB',$this->currentRow,"Consignee Street Address");
        $this->setColumnValueAndWidth('AC',$this->currentRow,"Consignee City");
        $this->setColumnValueAndWidth('AD',$this->currentRow,"Consignee State or Province");
        $this->setColumnValueAndWidth('AE',$this->currentRow,"Consignee Postal Code");
        $this->setColumnValueAndWidth('AF',$this->currentRow,"Consignee Country");
        $this->setColumnValueAndWidth('AG',$this->currentRow,"Cargo Piece Count");
        $this->setColumnValueAndWidth('AH',$this->currentRow,"Cargo Weight");
        $this->setColumnValueAndWidth('AI',$this->currentRow,"Cargo Weight UOM");
        $this->setColumnValueAndWidth('AJ',$this->currentRow,"Cargo Description");
        $this->setColumnValueAndWidth('AK',$this->currentRow,"Air AMS Participant Code");
        $this->setColumnValueAndWidth('AL',$this->currentRow,"Inbond Destination Airport");
        $this->setColumnValueAndWidth('AM',$this->currentRow,"Inbond Destination Type");
        $this->setColumnValueAndWidth('AN',$this->currentRow,"Bonded Carrier ID");
        $this->setColumnValueAndWidth('AO',$this->currentRow,"Onward Carrier");
        $this->setColumnValueAndWidth('AP',$this->currentRow,"Transfer FIRMS");
        $this->setColumnValueAndWidth('AQ',$this->currentRow,"Inbond Control Number");
        $this->setColumnValueAndWidth('AR',$this->currentRow,"FDA Indicator");
        $this->setColumnValueAndWidth('AS',$this->currentRow,"IncludeType86");
        $this->setColumnValueAndWidth('AT',$this->currentRow,"Entry Type");
        $this->setColumnValueAndWidth('AU',$this->currentRow,"T86 Date of Arrival");
        $this->setColumnValueAndWidth('AV',$this->currentRow,"IOR Type");
        $this->setColumnValueAndWidth('AW',$this->currentRow,"IOR Number");
        $this->setColumnValueAndWidth('AX',$this->currentRow,"Mode of Transport");
        $this->setColumnValueAndWidth('AY',$this->currentRow,"Bond Type");
        $this->setColumnValueAndWidth('AZ',$this->currentRow,"Cargo Location FIRMS");

        $this->setColumnValueAndWidth('BA',$this->currentRow,"Surety Code");
        $this->setColumnValueAndWidth('BB',$this->currentRow,"Bond Amount");
        $this->setColumnValueAndWidth('BC',$this->currentRow,"Additional Reference Code A");
        $this->setColumnValueAndWidth('BD',$this->currentRow,"Additional Reference Number A");
        $this->setColumnValueAndWidth('BE',$this->currentRow,"Additional Reference Code B");
        $this->setColumnValueAndWidth('BF',$this->currentRow,"Additional Reference Number B");
        $this->setColumnValueAndWidth('BG',$this->currentRow,"Port of Entry");
        $this->setColumnValueAndWidth('BH',$this->currentRow,"Equipment Number");
        $this->setColumnValueAndWidth('BI',$this->currentRow,"HTS Number 1");
        $this->setColumnValueAndWidth('BJ',$this->currentRow,"Description 1");
        $this->setColumnValueAndWidth('BK',$this->currentRow,"Line Item Value 1");
        $this->setColumnValueAndWidth('BL',$this->currentRow,"Country of Origin 1");
        $this->setColumnValueAndWidth('BM',$this->currentRow,"PGA FDA Disclaimer 1");
        $this->setColumnValueAndWidth('BN',$this->currentRow,"Prior Notice Confirmation Number 1");
        $this->setColumnValueAndWidth('BO',$this->currentRow,"HTS Number 2");
        $this->setColumnValueAndWidth('BP',$this->currentRow,"Description 2");
        $this->setColumnValueAndWidth('BQ',$this->currentRow,"Line Item Value 2");
        $this->setColumnValueAndWidth('BR',$this->currentRow,"Country of Origin 2");
        $this->setColumnValueAndWidth('BS',$this->currentRow,"PGA FDA Disclaimer 2");
        $this->setColumnValueAndWidth('BT',$this->currentRow,"Prior Notice Confirmation Number 2");
        $this->setColumnValueAndWidth('BU',$this->currentRow,"HTS Number 3");
        $this->setColumnValueAndWidth('BV',$this->currentRow,"Description 3");
        $this->setColumnValueAndWidth('BW',$this->currentRow,"Line Item Value 3");
        $this->setColumnValueAndWidth('BX',$this->currentRow,"Country of Origin 3");
        $this->setColumnValueAndWidth('BY',$this->currentRow,"PGA FDA Disclaimer 3");
        $this->setColumnValueAndWidth('BZ',$this->currentRow,"Prior Notice Confirmation Number 3");

        $this->setColumnValueAndWidth('CA',$this->currentRow,"HTS Number 4");
        $this->setColumnValueAndWidth('CB',$this->currentRow,"Description 4");
        $this->setColumnValueAndWidth('CC',$this->currentRow,"Line Item Value 4");
        $this->setColumnValueAndWidth('CD',$this->currentRow,"Country of Origin 4");
        $this->setColumnValueAndWidth('CE',$this->currentRow,"PGA FDA Disclaimer 4");
        $this->setColumnValueAndWidth('CF',$this->currentRow,"Prior Notice Confirmation Number 4");
        $this->setColumnValueAndWidth('CG',$this->currentRow,"HTS Number 5");
        $this->setColumnValueAndWidth('CH',$this->currentRow,"Description 5");
        $this->setColumnValueAndWidth('CI',$this->currentRow,"Line Item Value 5");
        $this->setColumnValueAndWidth('CJ',$this->currentRow,"Country of Origin 5");
        $this->setColumnValueAndWidth('CK',$this->currentRow,"PGA FDA Disclaimer 5");
        $this->setColumnValueAndWidth('CL',$this->currentRow,"Prior Notice Confirmation Number 5");
        $this->setColumnValueAndWidth('CM',$this->currentRow,"HTS Number 6");
        $this->setColumnValueAndWidth('CN',$this->currentRow,"Description 6");
        $this->setColumnValueAndWidth('CO',$this->currentRow,"Line Item Value 6");
        $this->setColumnValueAndWidth('CP',$this->currentRow,"Country of Origin 6");
        $this->setColumnValueAndWidth('CQ',$this->currentRow,"PGA FDA Disclaimer 6");
        $this->setColumnValueAndWidth('CR',$this->currentRow,"Prior Notice Confirmation Number 6");
        $this->setColumnValueAndWidth('CS',$this->currentRow,"HTS Number 7");
        $this->setColumnValueAndWidth('CT',$this->currentRow,"Description 7");
        $this->setColumnValueAndWidth('CU',$this->currentRow,"Line Item Value 7");
        $this->setColumnValueAndWidth('CV',$this->currentRow,"Country of Origin 7");
        $this->setColumnValueAndWidth('CW',$this->currentRow,"PGA FDA Disclaimer 7");
        $this->setColumnValueAndWidth('CX',$this->currentRow,"Prior Notice Confirmation Number 7");
        $this->setColumnValueAndWidth('CY',$this->currentRow,"HTS Number 8");
        $this->setColumnValueAndWidth('CZ',$this->currentRow,"Description 8");


        $this->setColumnValueAndWidth('DA',$this->currentRow,"Line Item Value 8");
        $this->setColumnValueAndWidth('DB',$this->currentRow,"Country of Origin 8");
        $this->setColumnValueAndWidth('DC',$this->currentRow,"PGA FDA Disclaimer 8");
        $this->setColumnValueAndWidth('DD',$this->currentRow,"Prior Notice Confirmation Number 8"); 




        
        $this->setBackgroundColor('A',$this->currentRow.':DD',$this->currentRow, "f2f2f2");
        $this->setColor('A',$this->currentRow.':DD',$this->currentRow, "000");
        $this->currentRow++;    
    }
    public function setColumnValueAndWidth($column,$row,$value,$width=30)
    {
        $this->setColumnWidth($column, $width);
         $this->setCellValue($column.$row, $value);  
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
