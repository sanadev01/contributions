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
                 $this->setCellValue('A'.$row, 'Master');
                 $this->setCellValue('B'.$row, $container->awb);
                 $this->setCellValue('C'.$row, $container->awb);
                 $this->setCellValue('D'.$row, $container->awb);
                 $this->setCellValue('E'.$row, $package->corrios_tracking_code);  
                 $this->setCellValue('F'.$row, $container->origin_airport);  
                 $this->setCellValue('G'.$row, '');  
                 $this->setCellValue('H'.$row, $container->created_at->addDays(1));  
                 $this->setCellValue('I'.$row, '');  
                 $this->setCellValue('J'.$row, $container->destination_operator_name); 
                 $this->setCellValue('K'.$row, '');  
                 $this->setCellValue('L'.$row, $container->flight_number);  
                 $this->setCellValue('M'.$row, $container->created_at->addDays(1));   
                 $this->setCellValue('N'.$row, '');  
                 $this->setCellValue('O'.$row, count($this->deliveryBill->containers));
                 $this->setCellValue('P'.$row, $this->deliveryBill->getWeight());  
                 $this->setCellValue('Q'.$row, '');
                 $this->setCellValue('R'.$row, '');
                 $this->setCellValue('S'.$row, '');
                 $this->setCellValue('T'.$row, '');
                 $this->setCellValue('U'.$row, $package->getSenderFullName());
                 $this->setCellValue('V'.$row, ($package->sender_address) ? $package->sender_address: '2200 NW 129TH AVE');
                 $this->setCellValue('W'.$row, ($package->sender_city) ? $package->sender_city: 'Miami');
                 $this->setCellValue('X'.$row, ($package->sender_state_id) ? $package->senderState->code: 'FL');
                 $this->setCellValue('Y'.$row, ($package->sender_zipcode) ? $package->sender_zipcode: '33182');
                 $this->setCellValue('Z'.$row, ($package->sender_zipcode) ? $package->sender_zipcode: 'US');
                 $this->setCellValue('AA'.$row, $package->recipient->getFullName());
                 $this->setCellValue('AB'.$row, $package->warehouse_number);
                 $this->setCellValue('AC'.$row, ($package->sender_phone) ? $package->sender_phone: '');
                 $this->setCellValue('AD'.$row, ($package->recipient->tax_id) ? $package->recipient->tax_id: '');
                 $this->setCellValue('AE'.$row, $package->recipient->address.' '.optional($package->recipient)->address2.' '.$package->recipient->street_no);
                 $this->setCellValue('AF'.$row, $package->recipient->city);
                 $this->setCellValue('AG'.$row, $package->recipient->State->code);
                 $this->setCellValue('AH'.$row, $package->recipient->zipcode);
                 $this->setCellValue('AI'.$row, $package->recipient->country->code);
                 $this->setCellValue('AJ'.$row, $container->getPiecesCount());
                 $this->setCellValue('AK'.$row, $this->deliveryBill->getWeight());
                 $this->setCellValue('AL'.$row, 'KG');
                 $this->setCellValue('AM'.$row, ($package->recipient->phone) ? $package->recipient->phone: '',);
                 $this->setCellValue('AN'.$row, '');
                 $this->setCellValue('AO'.$row, '');
                 $this->setCellValue('AP'.$row, '');
                 $this->setCellValue('AQ'.$row, '');
                 $this->setCellValue('AR'.$row, '');
                 $this->setCellValue('AS'.$row, '');
                 $this->setCellValue('AT'.$row, '');
                 $this->setCellValue('AU'.$row, '');
                 $this->setCellValue('AV'.$row, '');
                 $this->setCellValue('AW'.$row, '');
                 $this->setCellValue('AX'.$row, '');
                 $this->setCellValue('AY'.$row, '');
                 $this->setCellValue('AZ'.$row, '');
                 $this->setCellValue('BA'.$row, '');
                 $this->setCellValue('BB'.$row, '');
                 $this->setCellValue('BC'.$row, '');
                 $this->setCellValue('BD'.$row, '');
                 $this->setCellValue('BE'.$row, '');
                 $this->setCellValue('BF'.$row, '');
                 $this->setCellValue('BH'.$row, '');
                 $this->setCellValue('BI'.$row, '');
                 $this->setCellValue('BJ'.$row, '');
                 $this->setCellValue('BK'.$row, '');
                 $this->setCellValue('BL'.$row, '');
                 $this->setCellValue('BM'.$row, '');
                 $this->setCellValue('BN'.$row, '');
                 $this->setCellValue('BO'.$row, '');
                 $this->setCellValue('BP'.$row, '');
                 $this->setCellValue('BQ'.$row, '');
                 $this->setCellValue('BR'.$row, '');
                 $this->setCellValue('BS'.$row, '');
                 $this->setCellValue('BT'.$row, '');
                 $this->setCellValue('BU'.$row, '');
                 $this->setCellValue('BV'.$row, '');
                 $this->setCellValue('BW'.$row, '');
                 $this->setCellValue('BX'.$row, '');
                 $this->setCellValue('BY'.$row, '');
                 $this->setCellValue('BZ'.$row, '');
                 
                 $this->setCellValue('CA'.$row, '');
                 $this->setCellValue('CB'.$row, '');
                 $this->setCellValue('CC'.$row, '');
                 $this->setCellValue('CD'.$row, '');
                 $this->setCellValue('CE'.$row, '');
                 $this->setCellValue('CF'.$row, '');
                 $this->setCellValue('CG'.$row, '');
                 $this->setCellValue('CH'.$row, '');
                 $this->setCellValue('CI'.$row, '');
                 $this->setCellValue('CJ'.$row, '');
                 $this->setCellValue('CK'.$row, '');
                 $this->setCellValue('CI'.$row, '');
                 $this->setCellValue('CJ'.$row, '');
                 $this->setCellValue('CK'.$row, '');
                 $this->setCellValue('CL'.$row, '');
                 $this->setCellValue('CM'.$row, '');
                 $this->setCellValue('CN'.$row, '');
                 $this->setCellValue('CO'.$row, '');
                 $this->setCellValue('CP'.$row, '');
                 $this->setCellValue('CQ'.$row, '');
                 $this->setCellValue('CR'.$row, '');
                 $this->setCellValue('CS'.$row, '');
                 $this->setCellValue('CT'.$row, '');
                 $this->setCellValue('CU'.$row, '');
                 $this->setCellValue('CV'.$row, '');
                 $this->setCellValue('CW'.$row, '');
                 $this->setCellValue('CX'.$row, '');
                 $this->setCellValue('CY'.$row, '');
                 $this->setCellValue('CZ'.$row, '');

                 $this->setCellValue('DA'.$row, '');
                 $this->setCellValue('DB'.$row, '');
                 $this->setCellValue('DC'.$row, '');
                 $this->setCellValue('DD'.$row, '');
                 $this->setCellValue('DE'.$row, '');
                 $this->setCellValue('DF'.$row, '');
                 $this->setCellValue('DG'.$row, '');
                 $this->setCellValue('DH'.$row, '');
                 $this->setCellValue('DI'.$row, '');
                 $this->setCellValue('DJ'.$row, '');
                 $this->setCellValue('DK'.$row, '');
                 $this->setCellValue('DI'.$row, '');
                 $this->setCellValue('DJ'.$row, '');
                 $this->setCellValue('DK'.$row, '');
                 $this->setCellValue('DL'.$row, '');
                 $this->setCellValue('DM'.$row, '');
                 $this->setCellValue('DN'.$row, '');
                 $this->setCellValue('DO'.$row, '');
                 $this->setCellValue('DP'.$row, '');
                 $this->setCellValue('DQ'.$row, '');
                 $this->setCellValue('DR'.$row, '');
                 $this->setCellValue('DS'.$row, '');
                 $this->setCellValue('DT'.$row, '');
                 $this->setCellValue('DU'.$row, '');
                 $this->setCellValue('DV'.$row, '');
                 $this->setCellValue('DW'.$row, '');
                 $this->setCellValue('DX'.$row, '');
                 $this->setCellValue('DY'.$row, '');
                 $this->setCellValue('DZ'.$row, '');
                 
                 $this->setCellValue('EA'.$row, '');
                 $this->setCellValue('EB'.$row, '');
                 $this->setCellValue('EC'.$row, '');
                 $this->setCellValue('ED'.$row, '');
                 $this->setCellValue('EE'.$row, '');
                 $this->setCellValue('EF'.$row, '');
                 $this->setCellValue('EG'.$row, '');
                 $this->setCellValue('EH'.$row, '');
                 $this->setCellValue('EI'.$row, '');
                 $this->setCellValue('EJ'.$row, '');
                 $this->setCellValue('EK'.$row, '');
                 $this->setCellValue('EI'.$row, '');
                 $this->setCellValue('EJ'.$row, '');
                 $this->setCellValue('EK'.$row, '');
                 $this->setCellValue('EL'.$row, '');
                 $this->setCellValue('EM'.$row, '');
                 $this->setCellValue('EN'.$row, '');
                 $this->setCellValue('EO'.$row, '');
                 $this->setCellValue('EP'.$row, '');
                 $this->setCellValue('EQ'.$row, '');
                 $this->setCellValue('ER'.$row, '');
                 $this->setCellValue('ES'.$row, '');
                 $this->setCellValue('ET'.$row, '');
                 $this->setCellValue('EU'.$row, '');
                 $this->setCellValue('EV'.$row, '');
                 $this->setCellValue('EW'.$row, '');
                 $this->setCellValue('EX'.$row, '');
                 $this->setCellValue('EY'.$row, '');
                 $this->setCellValue('EZ'.$row, '');

                 $this->setCellValue('FA'.$row, '');
                 $this->setCellValue('FB'.$row, '');
                 $this->setCellValue('FC'.$row, '');
                 $this->setCellValue('FD'.$row, '');
                 $this->setCellValue('FE'.$row, '');
                 $this->setCellValue('FF'.$row, '');
                 $this->setCellValue('FG'.$row, '');
                 $this->setCellValue('FH'.$row, '');
                 $this->setCellValue('FI'.$row, '');
                 $this->setCellValue('FJ'.$row, '');
                 $this->setCellValue('FK'.$row, '');
                 $this->setCellValue('FI'.$row, '');
                 $this->setCellValue('FJ'.$row, '');
                 $this->setCellValue('FK'.$row, '');
                 $this->setCellValue('FL'.$row, '');
                 $this->setCellValue('FM'.$row, '');
                 $this->setCellValue('FN'.$row, '');
                 $this->setCellValue('FO'.$row, '');
                 $this->setCellValue('FP'.$row, '');
                 $this->setCellValue('FQ'.$row, '');
                 $this->setCellValue('FR'.$row, '');
                 $this->setCellValue('FS'.$row, '');
                 $this->setCellValue('FT'.$row, '');
                 $this->setCellValue('FU'.$row, '');
                 $this->setCellValue('FV'.$row, '');
                 $this->setCellValue('FW'.$row, '');
                 $this->setCellValue('FX'.$row, '');
                 $this->setCellValue('FY'.$row, '');
                 $this->setCellValue('FZ'.$row, '');

                 $this->setCellValue('GA'.$row, '');
                 $this->setCellValue('GB'.$row, '');
                 $this->setCellValue('GC'.$row, '');
                 $this->setCellValue('GD'.$row, '');
                 $this->setCellValue('GE'.$row, '');
                 $this->setCellValue('GF'.$row, '');
                 $this->setCellValue('GG'.$row, '');
                 $this->setCellValue('GH'.$row, '');
                 $this->setCellValue('GI'.$row, '');
                 $this->setCellValue('GJ'.$row, '');
                 $this->setCellValue('GK'.$row, '');
                 $this->setCellValue('GI'.$row, '');
                 $this->setCellValue('GJ'.$row, '');
                 $this->setCellValue('GK'.$row, '');
                 $this->setCellValue('GL'.$row, '');
                 $this->setCellValue('GM'.$row, '');
                 $this->setCellValue('GN'.$row, '');
                 $this->setCellValue('GO'.$row, '');
                 $this->setCellValue('GP'.$row, '');
                 $this->setCellValue('GQ'.$row, '');
                 $this->setCellValue('GR'.$row, '');
                 $this->setCellValue('GS'.$row, '');
                 $this->setCellValue('GT'.$row, '');
                 $this->setCellValue('GU'.$row, '');
                 $this->setCellValue('GV'.$row, '');
                 $this->setCellValue('GW'.$row, '');
                 $this->setCellValue('GX'.$row, '');
                 $this->setCellValue('GY'.$row, '');
                 $this->setCellValue('GZ'.$row, '');

                 $this->setCellValue('HA'.$row, '');
                 $this->setCellValue('HB'.$row, '');
                 $this->setCellValue('HC'.$row, '');
                 $this->setCellValue('HD'.$row, '');
                 $this->setCellValue('HE'.$row, '');
                 $this->setCellValue('HF'.$row, '');
                 $this->setCellValue('HG'.$row, '');
                 $this->setCellValue('HH'.$row, '');
                 $this->setCellValue('HI'.$row, '');
                 $this->setCellValue('HJ'.$row, '');
                 $this->setCellValue('HK'.$row, '');
                 $this->setCellValue('HI'.$row, '');
                 $this->setCellValue('HJ'.$row, '');
                 $this->setCellValue('HK'.$row, '');
                 $this->setCellValue('HL'.$row, '');
                 $this->setCellValue('HM'.$row, '');
                 $this->setCellValue('HN'.$row, '');
                 $this->setCellValue('HO'.$row, '');
                 $this->setCellValue('HP'.$row, '');
                 $this->setCellValue('HQ'.$row, '');
                 $this->setCellValue('HR'.$row, '');
                 $this->setCellValue('HS'.$row, '');
                 $this->setCellValue('HT'.$row, '');
                 $this->setCellValue('HU'.$row, '');
                 $this->setCellValue('HV'.$row, '');
                 $this->setCellValue('HW'.$row, '');
                 $this->setCellValue('HX'.$row, '');
                 $this->setCellValue('HY'.$row, '');
                 $this->setCellValue('HZ'.$row, '');

                 $this->setCellValue('IA'.$row, '');
                 $this->setCellValue('IB'.$row, '');
                 $this->setCellValue('IC'.$row, '');
                 $this->setCellValue('ID'.$row, '');












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
        $this->setColumnValueAndWidth('E',$this->currentRow, "Package Track No");
        $this->setColumnValueAndWidth('F',$this->currentRow, "Airport Of Origin");
        $this->setColumnValueAndWidth('G',$this->currentRow, "Permit to Proceed Destination Airport");
        $this->setColumnValueAndWidth('H',$this->currentRow, "Date of Arrival at the Permit to Proceed Destination Airport");
        $this->setColumnValueAndWidth('I',$this->currentRow, "Airport Of Arrival");
        $this->setColumnValueAndWidth('J',$this->currentRow, "Cargo Terminal Operator");
        $this->setColumnValueAndWidth('K',$this->currentRow, "Importing Carrier Air Carrier Code");
        $this->setColumnValueAndWidth('L',$this->currentRow, "Flight Number");
        $this->setColumnValueAndWidth('M',$this->currentRow, "Scheduled Date Of Arrival");
        $this->setColumnValueAndWidth('N',$this->currentRow, "Boarded Quantity Identifier");
        $this->setColumnValueAndWidth('O',$this->currentRow, "Boarded Pieces");
        $this->setColumnValueAndWidth('P',$this->currentRow, "Boarded Weight");
        $this->setColumnValueAndWidth('Q',$this->currentRow, "Boarded Weight Code");
        $this->setColumnValueAndWidth('R',$this->currentRow, "Part Arrival Reference");
        $this->setColumnValueAndWidth('S',$this->currentRow, "Food Facility Reg Exemption");
        $this->setColumnValueAndWidth('T',$this->currentRow, "Manufacturer Registration Number");
        $this->setColumnValueAndWidth('U',$this->currentRow, "Shipper Name");
        $this->setColumnValueAndWidth('V',$this->currentRow, "Shipper Street Address");
        $this->setColumnValueAndWidth('W',$this->currentRow, "Shipper City");
        $this->setColumnValueAndWidth('X',$this->currentRow, "Shipper State or Province");
        $this->setColumnValueAndWidth('Y',$this->currentRow, "Shipper Postal Code");
        $this->setColumnValueAndWidth('Z',$this->currentRow, "Shipper Country");
        
        $this->setColumnValueAndWidth('AA',$this->currentRow,"Shipper Telephone Number");
        $this->setColumnValueAndWidth('AB',$this->currentRow, "Consignee Name");
        $this->setColumnValueAndWidth('AC',$this->currentRow, "Consignee Identifier Code");
        $this->setColumnValueAndWidth('AD',$this->currentRow,"Consignee Tax ID");
        $this->setColumnValueAndWidth('AE',$this->currentRow,"Consignee Street Address");
        $this->setColumnValueAndWidth('AF',$this->currentRow,"Consignee City");
        $this->setColumnValueAndWidth('AG',$this->currentRow,"Consignee State or Province");
        $this->setColumnValueAndWidth('AH',$this->currentRow,"Consignee Postal Code");
        $this->setColumnValueAndWidth('AI',$this->currentRow,"Consignee Country");
        $this->setColumnValueAndWidth('AJ',$this->currentRow,"Cargo Piece Count");
        $this->setColumnValueAndWidth('AK',$this->currentRow,"Cargo Weight");
        $this->setColumnValueAndWidth('AL',$this->currentRow,"Cargo Weight UOM");
        $this->setColumnValueAndWidth('AM',$this->currentRow,"Consignee Telephone Number");
        $this->setColumnValueAndWidth('AN',$this->currentRow,"Cargo Description");
        $this->setColumnValueAndWidth('AO',$this->currentRow,"Marks and Numbers");
        $this->setColumnValueAndWidth('AP',$this->currentRow,"Air AMS Participant Code");
        $this->setColumnValueAndWidth('AQ',$this->currentRow,"Inbond Destination Airport");
        $this->setColumnValueAndWidth('AR',$this->currentRow,"Inbond Destination Type");
        $this->setColumnValueAndWidth('AS',$this->currentRow,"Bonded Carrier ID");
        $this->setColumnValueAndWidth('AT',$this->currentRow,"Onward Carrier");
        $this->setColumnValueAndWidth('AU',$this->currentRow,"Transfer FIRMS");
        $this->setColumnValueAndWidth('AV',$this->currentRow,"Inbond Control Number");
        $this->setColumnValueAndWidth('AW',$this->currentRow,"FDA Indicator");
        $this->setColumnValueAndWidth('AX',$this->currentRow,"ACAS Filing");
        $this->setColumnValueAndWidth('AY',$this->currentRow,"IncludeType86");
        $this->setColumnValueAndWidth('AZ',$this->currentRow,"Entry Type");

        $this->setColumnValueAndWidth('BA',$this->currentRow,"T86 Date of Arrival");
        $this->setColumnValueAndWidth('BB',$this->currentRow,"IOR Type");
        $this->setColumnValueAndWidth('BC',$this->currentRow,"IOR Number");
        $this->setColumnValueAndWidth('BD',$this->currentRow,"Mode of Transport");
        $this->setColumnValueAndWidth('BE',$this->currentRow,"Bond Type");
        $this->setColumnValueAndWidth('BF',$this->currentRow,"Cargo Location FIRMS");
        $this->setColumnValueAndWidth('BG',$this->currentRow,"Surety Code");
        $this->setColumnValueAndWidth('BH',$this->currentRow,"Bond Amount");
        $this->setColumnValueAndWidth('BI',$this->currentRow,"Express Consigment Shipment");
        $this->setColumnValueAndWidth('BJ',$this->currentRow,"Known Importer");
        $this->setColumnValueAndWidth('BK',$this->currentRow,"Perishable Goods");
        $this->setColumnValueAndWidth('BL',$this->currentRow,"Additional Reference Code A");
        $this->setColumnValueAndWidth('BM',$this->currentRow,"Additional Reference Number A");
        $this->setColumnValueAndWidth('BN',$this->currentRow,"Additional Reference Code B");
        $this->setColumnValueAndWidth('BO',$this->currentRow,"Additional Reference Number B");
        $this->setColumnValueAndWidth('BP',$this->currentRow,"Port of Entry");
        $this->setColumnValueAndWidth('BQ',$this->currentRow,"Equipment Number");
        $this->setColumnValueAndWidth('BR',$this->currentRow,"HTS Number 1");
        $this->setColumnValueAndWidth('BS',$this->currentRow,"Description 1");
        $this->setColumnValueAndWidth('BT',$this->currentRow,"Line Item Value 1");
        $this->setColumnValueAndWidth('BU',$this->currentRow,"Country of Origin 1");
        $this->setColumnValueAndWidth('BV',$this->currentRow,"PGA FDA Disclaimer 1");
        $this->setColumnValueAndWidth('BW',$this->currentRow,"Prior Notice Confirmation Number 1");
        $this->setColumnValueAndWidth('BX',$this->currentRow,"PGA Product ID 1");
        $this->setColumnValueAndWidth('BY',$this->currentRow,"PGA Packaging Qty 1 - 1");
        $this->setColumnValueAndWidth('BZ',$this->currentRow,"PGA Packaging UOM 1 - 1");

        $this->setColumnValueAndWidth('CA',$this->currentRow,"PGA Packaging Qty 1 - 2");
        $this->setColumnValueAndWidth('CB',$this->currentRow,"PGA Packaging UOM 1 - 2");
        $this->setColumnValueAndWidth('CC',$this->currentRow,"PGA Packaging Qty 1 - 3");
        $this->setColumnValueAndWidth('CD',$this->currentRow,"PGA Packaging UOM 1 - 3");
        $this->setColumnValueAndWidth('CE',$this->currentRow,"PGA Packaging Qty 1 - 4");
        $this->setColumnValueAndWidth('CF',$this->currentRow,"PGA Packaging UOM 1 - 4");
        $this->setColumnValueAndWidth('CG',$this->currentRow,"PGA Packaging QTY 1 - 5");
        $this->setColumnValueAndWidth('CH',$this->currentRow,"PGA Packaging UOM 1 - 5");
        $this->setColumnValueAndWidth('CI',$this->currentRow,"PGA Packaging QTY 1 - 1");
        $this->setColumnValueAndWidth('CJ',$this->currentRow,"PGA Packaging UOM 1 - 6");
        $this->setColumnValueAndWidth('CK',$this->currentRow,"Gross Net Quantity 1");
        $this->setColumnValueAndWidth('CL',$this->currentRow,"Gross Net UOM 1");
        $this->setColumnValueAndWidth('CM',$this->currentRow,"HTS Number 2");
        $this->setColumnValueAndWidth('CN',$this->currentRow,"Description 2");
        $this->setColumnValueAndWidth('CO',$this->currentRow,"Line Item Value 2");
        $this->setColumnValueAndWidth('CP',$this->currentRow,"Country of Origin 2");
        $this->setColumnValueAndWidth('CQ',$this->currentRow,"PGA FDA Disclaimer 2");
        $this->setColumnValueAndWidth('CR',$this->currentRow,"PGA Product ID 2");
        $this->setColumnValueAndWidth('CS',$this->currentRow,"Prior Notice Confirmation Number 2");
        $this->setColumnValueAndWidth('CT',$this->currentRow,"PGA Packaging Qty 2 - 1");
        $this->setColumnValueAndWidth('CU',$this->currentRow,"PGA Packaging UOM 2 - 1");
        $this->setColumnValueAndWidth('CV',$this->currentRow,"PGA Packaging Qty 2 - 2");
        $this->setColumnValueAndWidth('CW',$this->currentRow,"PGA Packaging UOM 2 - 2");
        $this->setColumnValueAndWidth('CX',$this->currentRow,"PGA Packaging Qty 2 - 3");
        $this->setColumnValueAndWidth('CY',$this->currentRow,"PGA Packaging UOM 2 - 3");
        $this->setColumnValueAndWidth('CZ',$this->currentRow,"PGA Packaging Qty 2 - 4");
        $this->setColumnValueAndWidth('DA',$this->currentRow,"PGA Packaging UOM 2 - 4");
        $this->setColumnValueAndWidth('DB',$this->currentRow,"PGA Packaging Qty 2 - 5");
        $this->setColumnValueAndWidth('DC',$this->currentRow,"PGA Packaging UOM 2 - 5");
        $this->setColumnValueAndWidth('DD',$this->currentRow,"PGA Packaging Qty 2 - 6");
        $this->setColumnValueAndWidth('DE',$this->currentRow,"PGA Packaging UOM 2 - 6");
        $this->setColumnValueAndWidth('DF',$this->currentRow,"Gross Net Quantity 2");
        $this->setColumnValueAndWidth('DG',$this->currentRow,"Gross Net UOM 2");


        $this->setColumnValueAndWidth('DH',$this->currentRow,"HTS Number 3");
        $this->setColumnValueAndWidth('DI',$this->currentRow,"Description 3");
        $this->setColumnValueAndWidth('DJ',$this->currentRow,"Line Item Value 3");
        $this->setColumnValueAndWidth('DK',$this->currentRow,"Country of Origin 3");
        $this->setColumnValueAndWidth('DL',$this->currentRow,"PGA FDA Disclaimer 3");
        $this->setColumnValueAndWidth('DM',$this->currentRow,"PGA Product ID 3");
        $this->setColumnValueAndWidth('DN',$this->currentRow,"Prior Notice Confirmation Number 3");
        $this->setColumnValueAndWidth('DO',$this->currentRow,"PGA Packaging Qty 3 - 1");
        $this->setColumnValueAndWidth('DP',$this->currentRow,"PGA Packaging UOM 3 - 1");
        $this->setColumnValueAndWidth('DQ',$this->currentRow,"PGA Packaging Qty 3 - 2");
        $this->setColumnValueAndWidth('DR',$this->currentRow,"PGA Packaging UOM 3 - 2");
        $this->setColumnValueAndWidth('DS',$this->currentRow,"PGA Packaging Qty 3 - 3");
        $this->setColumnValueAndWidth('DT',$this->currentRow,"PGA Packaging UOM 3 - 3");
        $this->setColumnValueAndWidth('DU',$this->currentRow,"PGA Packaging Qty 3 - 4");
        $this->setColumnValueAndWidth('DV',$this->currentRow,"PGA Packaging UOM 3 - 4");
        $this->setColumnValueAndWidth('DW',$this->currentRow,"PGA Packaging Qty 3 - 5");
        $this->setColumnValueAndWidth('DX',$this->currentRow,"PGA Packaging UOM 3 - 5");
        $this->setColumnValueAndWidth('DY',$this->currentRow,"PGA Packaging Qty 3 - 6");
        $this->setColumnValueAndWidth('DZ',$this->currentRow,"PGA Packaging UOM 3 - 6");

        $this->setColumnValueAndWidth('EA',$this->currentRow,"Gross Net Quantity 3");
        $this->setColumnValueAndWidth('EB',$this->currentRow,"Gross Net UOM 3");
        $this->setColumnValueAndWidth('EC',$this->currentRow,"HTS Number 4");
        $this->setColumnValueAndWidth('ED',$this->currentRow,"Description 4");
        $this->setColumnValueAndWidth('EE',$this->currentRow,"Line Item Value 4");
        $this->setColumnValueAndWidth('EF',$this->currentRow,"Country of Origin 4");
        $this->setColumnValueAndWidth('EG',$this->currentRow,"PGA FDA Disclaimer 4");
        $this->setColumnValueAndWidth('EH',$this->currentRow,"PGA Product ID 4");
        $this->setColumnValueAndWidth('EI',$this->currentRow,"Prior Notice Confirmation Number 4");
        $this->setColumnValueAndWidth('DJ',$this->currentRow,"PGA Packaging Qty 4 - 1");
        $this->setColumnValueAndWidth('EK',$this->currentRow,"PGA Packaging UOM 4 - 1");
        $this->setColumnValueAndWidth('EL',$this->currentRow,"PGA Packaging Qty 4 - 2");
        $this->setColumnValueAndWidth('EM',$this->currentRow,"PGA Packaging UOM 4 - 2");
        $this->setColumnValueAndWidth('EN',$this->currentRow,"PGA Packaging Qty 4 - 3");
        $this->setColumnValueAndWidth('EO',$this->currentRow,"PGA Packaging UOM 4 - 3");
        $this->setColumnValueAndWidth('EP',$this->currentRow,"PGA Packaging Qty 4 - 4");
        $this->setColumnValueAndWidth('EQ',$this->currentRow,"PGA Packaging UOM 4 - 4");
        $this->setColumnValueAndWidth('ER',$this->currentRow,"PGA Packaging Qty 4 - 5");
        $this->setColumnValueAndWidth('ES',$this->currentRow,"PGA Packaging UOM 4 - 5");
        $this->setColumnValueAndWidth('ET',$this->currentRow,"PGA Packaging Qty 4 - 6");
        $this->setColumnValueAndWidth('EU',$this->currentRow,"PGA Packaging UOM 4 - 6");
        $this->setColumnValueAndWidth('EV',$this->currentRow,"Gross Net Quantity 4");
        $this->setColumnValueAndWidth('EW',$this->currentRow,"Gross Net UOM 4");
        $this->setColumnValueAndWidth('EX',$this->currentRow,"HTS Number 5");
        $this->setColumnValueAndWidth('EY',$this->currentRow,"Description 5");
        $this->setColumnValueAndWidth('EZ',$this->currentRow,"Line Item Value 5");

        $this->setColumnValueAndWidth('FA',$this->currentRow,"Country of Origin 5");
        $this->setColumnValueAndWidth('FB',$this->currentRow,"PGA FDA Disclaimer 5");
        $this->setColumnValueAndWidth('FC',$this->currentRow,"PGA Product ID 5");
        $this->setColumnValueAndWidth('FD',$this->currentRow,"Prior Notice Confirmation Number 5");
        $this->setColumnValueAndWidth('FE',$this->currentRow,"PGA Packaging Qty 5 - 1");
        $this->setColumnValueAndWidth('FF',$this->currentRow,"PGA Packaging UOM 5 - 1");
        $this->setColumnValueAndWidth('FG',$this->currentRow,"PGA Packaging Qty 5 - 2");
        $this->setColumnValueAndWidth('FH',$this->currentRow,"PGA Packaging UOM 5 - 2");
        $this->setColumnValueAndWidth('FI',$this->currentRow,"PGA Packaging Qty 5 - 3");
        $this->setColumnValueAndWidth('FJ',$this->currentRow,"PGA Packaging UOM 5 - 3");
        $this->setColumnValueAndWidth('FK',$this->currentRow,"PGA Packaging Qty 5 - 4");
        $this->setColumnValueAndWidth('FL',$this->currentRow,"PGA Packaging UOM 5 - 4");
        $this->setColumnValueAndWidth('FM',$this->currentRow,"PGA Packaging Qty 5 - 5");
        $this->setColumnValueAndWidth('FN',$this->currentRow,"PGA Packaging UOM 5 - 5");
        $this->setColumnValueAndWidth('FO',$this->currentRow,"PGA Packaging Qty 5 - 6");
        $this->setColumnValueAndWidth('FP',$this->currentRow,"PGA Packaging UOM 5 - 6");
        $this->setColumnValueAndWidth('FQ',$this->currentRow,"Gross Net Quantity 5");
        $this->setColumnValueAndWidth('FR',$this->currentRow,"Gross Net UOM 5");
        $this->setColumnValueAndWidth('FS',$this->currentRow,"HTS Number 6");
        $this->setColumnValueAndWidth('FT',$this->currentRow,"Description 6");
        $this->setColumnValueAndWidth('FU',$this->currentRow,"Line Item Value 6");
        $this->setColumnValueAndWidth('FV',$this->currentRow,"Country of Origin 6");
        $this->setColumnValueAndWidth('FW',$this->currentRow,"PGA FDA Disclaimer 6");
        $this->setColumnValueAndWidth('FX',$this->currentRow,"PGA Product ID 6");
        $this->setColumnValueAndWidth('FY',$this->currentRow,"Prior Notice Confirmation Number 6");
        $this->setColumnValueAndWidth('FZ',$this->currentRow,"PGA Packaging Qty 6 - 1");

        $this->setColumnValueAndWidth('GA',$this->currentRow,"PGA Packaging UOM 6 - 1");
        $this->setColumnValueAndWidth('GB',$this->currentRow,"PGA Packaging Qty 6 - 2");
        $this->setColumnValueAndWidth('GC',$this->currentRow,"PGA Packaging UOM 6 - 2");
        $this->setColumnValueAndWidth('GD',$this->currentRow,"PGA Packaging Qty 6 - 3");
        $this->setColumnValueAndWidth('GE',$this->currentRow,"PGA Packaging UOM 6 - 3");
        $this->setColumnValueAndWidth('GF',$this->currentRow,"PGA Packaging Qty 6 - 4");
        $this->setColumnValueAndWidth('GG',$this->currentRow,"PGA Packaging UOM 6 - 4");
        $this->setColumnValueAndWidth('GH',$this->currentRow,"PGA Packaging Qty 6 - 5");
        $this->setColumnValueAndWidth('GI',$this->currentRow,"PGA Packaging UOM 6 - 5");
        $this->setColumnValueAndWidth('GJ',$this->currentRow,"PGA Packaging Qty 6 - 6");
        $this->setColumnValueAndWidth('GK',$this->currentRow,"PGA Packaging UOM 6 - 6");
        $this->setColumnValueAndWidth('GL',$this->currentRow,"Gross Net Quantity 6");
        $this->setColumnValueAndWidth('GM',$this->currentRow,"Gross Net UOM 6");
        $this->setColumnValueAndWidth('GN',$this->currentRow,"HTS Number 7");
        $this->setColumnValueAndWidth('GO',$this->currentRow,"Description 7");
        $this->setColumnValueAndWidth('GP',$this->currentRow,"Line Item Value 7");
        $this->setColumnValueAndWidth('GQ',$this->currentRow,"Country of Origin 7");
        $this->setColumnValueAndWidth('GR',$this->currentRow,"PGA FDA Disclaimer 7");
        $this->setColumnValueAndWidth('GS',$this->currentRow,"PGA Product ID 7");
        $this->setColumnValueAndWidth('GT',$this->currentRow,"Prior Notice Confirmation Number 7");
        $this->setColumnValueAndWidth('GU',$this->currentRow,"PGA Packaging Qty 7 - 1");
        $this->setColumnValueAndWidth('GV',$this->currentRow,"PGA Packaging UOM 7 - 1");
        $this->setColumnValueAndWidth('GW',$this->currentRow,"PGA Packaging Qty 7 - 2");
        $this->setColumnValueAndWidth('GX',$this->currentRow,"PGA Packaging UOM 7 - 2");
        $this->setColumnValueAndWidth('GY',$this->currentRow,"PGA Packaging Qty 7 - 3");
        $this->setColumnValueAndWidth('GZ',$this->currentRow,"PGA Packaging UOM 7 - 3");
        
        $this->setColumnValueAndWidth('HA',$this->currentRow,"PGA Packaging Qty 7 - 4");
        $this->setColumnValueAndWidth('HB',$this->currentRow,"PGA Packaging UOM 7 - 4");
        $this->setColumnValueAndWidth('HC',$this->currentRow,"PGA Packaging Qty 7 - 5");
        $this->setColumnValueAndWidth('HD',$this->currentRow,"PGA Packaging UOM 7 - 5");
        $this->setColumnValueAndWidth('HE',$this->currentRow,"PGA Packaging Qty 7 - 6");
        $this->setColumnValueAndWidth('HF',$this->currentRow,"PGA Packaging UOM 7 - 6");
        $this->setColumnValueAndWidth('HG',$this->currentRow,"Gross Net Quantity 7");
        $this->setColumnValueAndWidth('HH',$this->currentRow,"Gross Net UOM 7");
        $this->setColumnValueAndWidth('HI',$this->currentRow,"HTS Number 8");
        $this->setColumnValueAndWidth('HJ',$this->currentRow,"Description 8");
        $this->setColumnValueAndWidth('HK',$this->currentRow,"Line Item Value 8");
        $this->setColumnValueAndWidth('HL',$this->currentRow,"Country of Origin 8");
        $this->setColumnValueAndWidth('HM',$this->currentRow,"PGA FDA Disclaimer 8");
        $this->setColumnValueAndWidth('HN',$this->currentRow,"PGA Product ID 8");
        $this->setColumnValueAndWidth('HO',$this->currentRow,"Prior Notice Confirmation Number 8");
        $this->setColumnValueAndWidth('HP',$this->currentRow,"PGA Packaging Qty 8- 1");
        $this->setColumnValueAndWidth('HQ',$this->currentRow,"PGA Packaging UOM 8 - 1");
        $this->setColumnValueAndWidth('HR',$this->currentRow,"PGA Packaging Qty 8 - 2");
        $this->setColumnValueAndWidth('HS',$this->currentRow,"PGA Packaging UOM 8 - 2");
        $this->setColumnValueAndWidth('HT',$this->currentRow,"PGA Packaging Qty 8 - 3");
        $this->setColumnValueAndWidth('HU',$this->currentRow,"PGA Packaging UOM 8 - 3");
        $this->setColumnValueAndWidth('HV',$this->currentRow,"PGA Packaging Qty 8 - 4");
        $this->setColumnValueAndWidth('HW',$this->currentRow,"PGA Packaging UOM 8 - 4");
        $this->setColumnValueAndWidth('HX',$this->currentRow,"PGA Packaging Qty 8 - 5");
        $this->setColumnValueAndWidth('HY',$this->currentRow,"PGA Packaging UOM 8 - 5");
        $this->setColumnValueAndWidth('HZ',$this->currentRow,"PGA Packaging Qty 8 - 6");
        
        $this->setColumnValueAndWidth('IA',$this->currentRow,"PGA Packaging UOM 8 - 6");
        $this->setColumnValueAndWidth('IB',$this->currentRow,"Gross Net Quantity 8");
        $this->setColumnValueAndWidth('IC',$this->currentRow,"Gross Net UOM 8");
        $this->setColumnValueAndWidth('ID',$this->currentRow,"UOM 8");
    




        
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
