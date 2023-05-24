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
    private $uomMaxColumn = "CM";
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
                 $this->setCellValue('B'.$row, substr($container->awb, 0, 3));
                 $this->setCellValue('C'.$row, $container->awb);
                 $this->setCellValue('D'.$row, $package->corrios_tracking_code);
                 $this->setCellValue('E'.$row, $package->corrios_tracking_code);  
                 $this->setCellValue('F'.$row, $container->origin_airport);  
                 $this->setCellValue('G'.$row, '');  
                 $this->setCellValue('H'.$row, $container->created_at->addDays(1));  
                 $this->setCellValue('I'.$row, $container->destination_operator_name);  
                 $this->setCellValue('J'.$row, $container->destination_operator_name); 
                 $this->setCellValue('K'.$row, substr($container->flight_number, 0, 3));  
                 $this->setCellValue('L'.$row, $container->flight_number);  
                 $this->setCellValue('M'.$row, $container->created_at->addDays(1));   
                 $this->setCellValue('N'.$row, '');  
                 $this->setCellValue('O'.$row, count($this->deliveryBill->containers));
                 $this->setCellValue('P'.$row, $this->deliveryBill->getWeight());  
                 $this->setCellValue('Q'.$row, ($package->measurement_unit == "lbs/in") ? 'LB' : 'KG');
                 $this->setCellValue('R'.$row, '');
                 $this->setCellValue('S'.$row, '');
                 $this->setCellValue('T'.$row, '');
                 $this->setCellValue('U'.$row, $package->getSenderFullName());
                 $this->setCellValue('V'.$row, ($package->sender_address) ? $package->sender_address: '2200 NW 129TH AVE');
                 $this->setCellValue('W'.$row, ($package->sender_city) ? $package->sender_city: 'Miami');
                 $this->setCellValue('X'.$row, ($package->sender_state_id) ? $package->senderState->code: 'FL');
                 $this->setCellValue('Y'.$row, ($package->sender_zipcode) ? $package->sender_zipcode: '33182');
                 $this->setCellValue('Z'.$row, 'US');
                 $this->setCellValue('AA'.$row, ($package->sender_phone) ? $package->sender_phone: '');
                 $this->setCellValue('AB'.$row, $package->recipient->getFullName());
                 $this->setCellValue('AC'.$row, '');
                 $this->setCellValue('AD'.$row, '');
                 $this->setCellValue('AE'.$row, $package->recipient->address.' '.optional($package->recipient)->address2.' '.$package->recipient->street_no);
                 $this->setCellValue('AF'.$row, $package->recipient->city);
                 $this->setCellValue('AG'.$row, $package->recipient->State->code);
                 $this->setCellValue('AH'.$row, $package->recipient->zipcode);
                 $this->setCellValue('AI'.$row, $package->recipient->country->code);
                 $this->setCellValue('AJ'.$row, count($package->items));
                 $this->setCellValue('AK'.$row, $this->deliveryBill->getWeight());
                 $this->setCellValue('AL'.$row, ($package->measurement_unit == "lbs/in") ? 'LB' : 'KG');
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
                 $this->setCellValue('BD'.$row, 'Air');
                 $this->setCellValue('BE'.$row, '');
                 $this->setCellValue('BF'.$row, '');
                 $this->setCellValue('BG'.$row, '');
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

                 if(count($package->items)>0){
                    $this->setValueAndHeading($package,$package->items[0],$row,['BR','BS','BT','BU','BV','BW','BX','BY','BZ','CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL'],1);
                 $uomColumn = 'CM';
                }

                if(count($package->items)>1){
                    $this->setValueAndHeading($package,$package->items[1],$row,['CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ','DA','DB','DC','DD','DE','DF','DG'],2);
                    $uomColumn = 'DH';
               
                }
                
                if(count($package->items)>2){
                    $this->setValueAndHeading($package,$package->items[2],$row,['DH','DI','DJ', 'DK','DL','DM','DN','DO','DP','DQ','DR','DS','DT','DU','DV','DW','DX','DY','DZ','EA','EB'],3);
                    $uomColumn = 'EC';
                
                }
                                
                if(count($package->items)>3){
                    $this->setValueAndHeading($package,$package->items[3],$row,[ 'EC','ED','EE','EF','EG','EH','EI','EJ','EK','EL','EM','EN','EO','EP','EQ','ER','ES','ET','EU','EV','EW'],4);
                    $uomColumn = 'EX';
                
                }
               
                if(count($package->items)>4){
                    $this->setValueAndHeading($package,$package->items[4],$row,['EX','EY','EZ','FA','FB','FC','FD','FE','FF','FG','FH','FI','FJ','FK','FL','FM','FN','FO','FP','FQ','FR'],5);
                    $uomColumn = 'FS';
                
                }
               
                if(count($package->items)>5){
                    $this->setValueAndHeading($package,$package->items[5],$row,['FS','FT','FU','FV','FW','FX','FY','FZ','GA','GB','GC','GD','GE','GF','GG','GH','GI','GJ','GK','GL','GM'],6);
                    $uomColumn = 'GN';
                
                }
               
                if(count($package->items)>6){
                    $this->setValueAndHeading($package,$package->items[6],$row,['GN','GO','GP','GQ','GR','GS','GT','GU','GV','GW','GX','GY','GZ','HA','HB','HC','HD','HE','HF','HG','HH'],7);
                    $uomColumn = 'HI';
               
                }
               
                if(count($package->items)>7){
                    $this->setValueAndHeading($package,$package->items[7],$row,['HI','HJ','HK','HL','HM','HN','HO','HP','HQ','HR','HS','HT','HU','HV','HW','HX','HY','HZ','IA','IB','IC'],8);
                    $uomColumn = 'ID';
               
                }

                if($uomColumn > $this->uomMaxColumn){
                    $this->uomMaxColumn = $uomColumn;
                }
                $row++;
        
            }
        } 
        $this->currentRow = $row; 
        $newRow = 2;
        $this->setColumnValueAndWidth($this->uomMaxColumn,1,"UOM 8");
        foreach ($this->deliveryBill->containers as $container){
            foreach ($container->orders as $package) {
            $this->setColumnValueAndWidth($this->uomMaxColumn,$newRow,$package->measurement_unit); 
                $newRow++;
            }
        }
    }

    private function setValueAndHeading($package,$item,$row,$column,$number){

        $this->setColumnValueAndWidth($column[0],1,"HTS Number $number");
        $this->setColumnValueAndWidth($column[1],1,"Description $number");
        $this->setColumnValueAndWidth($column[2],1,"Line Item Value  $number");
        $this->setColumnValueAndWidth($column[3],1,"Country of Origin $number");
        $this->setColumnValueAndWidth($column[4],1,"PGA FDA Disclaimer $number");
        $this->setColumnValueAndWidth($column[5],1,"Prior Notice Confirmation Number $number");
        $this->setColumnValueAndWidth($column[6],1,"PGA Product ID $number");
        $this->setColumnValueAndWidth($column[7],1,"PGA Packaging Qty $number - 1");
        $this->setColumnValueAndWidth($column[8],1,"PGA Packaging UOM $number - 1");
        $this->setColumnValueAndWidth($column[9],1,"PGA Packaging Qty $number - 2");
        $this->setColumnValueAndWidth($column[10],1,"PGA Packaging UOM $number - 2");
        $this->setColumnValueAndWidth($column[11],1,"PGA Packaging Qty $number - 3");
        $this->setColumnValueAndWidth($column[12],1,"PGA Packaging UOM $number - 3");
        $this->setColumnValueAndWidth($column[13],1,"PGA Packaging Qty $number - 4");
        $this->setColumnValueAndWidth($column[14],1,"PGA Packaging UOM $number - 4");
        $this->setColumnValueAndWidth($column[15],1,"PGA Packaging QTY $number - 5");
        $this->setColumnValueAndWidth($column[16],1,"PGA Packaging UOM $number - 5");
        $this->setColumnValueAndWidth($column[17],1,"PGA Packaging QTY $number - 6");
        $this->setColumnValueAndWidth($column[18],1,"PGA Packaging UOM $number - 6");
        $this->setColumnValueAndWidth($column[19],1,"Gross Net Quantity $number");
        $this->setColumnValueAndWidth($column[20],1,"Gross Net UOM $number"); 

        $this->setColumnValueAndWidth($column[0],$row,$item->sh_code);
        $this->setColumnValueAndWidth($column[1],$row,$item->description);
        $this->setColumnValueAndWidth($column[2],$row,$item->value);
        $this->setColumnValueAndWidth($column[3],$row,"US");
        $this->setColumnValueAndWidth($column[4],$row,'');
        $this->setColumnValueAndWidth($column[5],$row,'');
        $this->setColumnValueAndWidth($column[6],$row,'');
        $this->setColumnValueAndWidth($column[7],$row,'');
        $this->setColumnValueAndWidth($column[8],$row,'');
        $this->setColumnValueAndWidth($column[9],$row,'');
       $this->setColumnValueAndWidth($column[10],$row,'');  
       $this->setColumnValueAndWidth($column[11],$row,'');  
       $this->setColumnValueAndWidth($column[12],$row,'');  
       $this->setColumnValueAndWidth($column[13],$row,'');  
       $this->setColumnValueAndWidth($column[14],$row,'');  
       $this->setColumnValueAndWidth($column[15],$row,'');  
       $this->setColumnValueAndWidth($column[16],$row,'');  
       $this->setColumnValueAndWidth($column[17],$row,'');  
       $this->setColumnValueAndWidth($column[18],$row,'');  
       $this->setColumnValueAndWidth($column[19],$row,'');  
       $this->setColumnValueAndWidth($column[20],$row,'');  

    }

    private function setExcelHeaderRow()
    {
        $row = $this->currentRow;
        $this->setColumnValueAndWidth('A',$row, "Waybill Typ"); 
        $this->setColumnValueAndWidth('B',$row, "AWB Prefix"); 
        $this->setColumnValueAndWidth('C',$row, "AWB Number"); 
        $this->setColumnValueAndWidth('D',$row, "HAWB Number"); 
        $this->setColumnValueAndWidth('E',$row, "Package Track No");
        $this->setColumnValueAndWidth('F',$row, "Airport Of Origin");
        $this->setColumnValueAndWidth('G',$row, "Permit to Proceed Destination Airport");
        $this->setColumnValueAndWidth('H',$row, "Date of Arrival at the Permit to Proceed Destination Airport");
        $this->setColumnValueAndWidth('I',$row, "Airport Of Arrival");
        $this->setColumnValueAndWidth('J',$row, "Cargo Terminal Operator");
        $this->setColumnValueAndWidth('K',$row, "Importing Carrier Air Carrier Code");
        $this->setColumnValueAndWidth('L',$row, "Flight Number");
        $this->setColumnValueAndWidth('M',$row, "Scheduled Date Of Arrival");
        $this->setColumnValueAndWidth('N',$row, "Boarded Quantity Identifier");
        $this->setColumnValueAndWidth('O',$row, "Boarded Pieces");
        $this->setColumnValueAndWidth('P',$row, "Boarded Weight");
        $this->setColumnValueAndWidth('Q',$row, "Boarded Weight Code");
        $this->setColumnValueAndWidth('R',$row, "Part Arrival Reference");
        $this->setColumnValueAndWidth('S',$row, "Food Facility Reg Exemption");
        $this->setColumnValueAndWidth('T',$row, "Manufacturer Registration Number");
        $this->setColumnValueAndWidth('U',$row, "Shipper Name");
        $this->setColumnValueAndWidth('V',$row, "Shipper Street Address");
        $this->setColumnValueAndWidth('W',$row, "Shipper City");
        $this->setColumnValueAndWidth('X',$row, "Shipper State or Province");
        $this->setColumnValueAndWidth('Y',$row, "Shipper Postal Code");
        $this->setColumnValueAndWidth('Z',$row, "Shipper Country");
        
        $this->setColumnValueAndWidth('AA',$row,"Shipper Telephone Number");
        $this->setColumnValueAndWidth('AB',$row, "Consignee Name");
        $this->setColumnValueAndWidth('AC',$row, "Consignee Identifier Code");
        $this->setColumnValueAndWidth('AD',$row,"Consignee Tax ID");
        $this->setColumnValueAndWidth('AE',$row,"Consignee Street Address");
        $this->setColumnValueAndWidth('AF',$row,"Consignee City");
        $this->setColumnValueAndWidth('AG',$row,"Consignee State or Province");
        $this->setColumnValueAndWidth('AH',$row,"Consignee Postal Code");
        $this->setColumnValueAndWidth('AI',$row,"Consignee Country");
        $this->setColumnValueAndWidth('AJ',$row,"Cargo Piece Count");
        $this->setColumnValueAndWidth('AK',$row,"Cargo Weight");
        $this->setColumnValueAndWidth('AL',$row,"Cargo Weight UOM");
        $this->setColumnValueAndWidth('AM',$row,"Consignee Telephone Number");
        $this->setColumnValueAndWidth('AN',$row,"Cargo Description");
        $this->setColumnValueAndWidth('AO',$row,"Marks and Numbers");
        $this->setColumnValueAndWidth('AP',$row,"Air AMS Participant Code");
        $this->setColumnValueAndWidth('AQ',$row,"Inbond Destination Airport");
        $this->setColumnValueAndWidth('AR',$row,"Inbond Destination Type");
        $this->setColumnValueAndWidth('AS',$row,"Bonded Carrier ID");
        $this->setColumnValueAndWidth('AT',$row,"Onward Carrier");
        $this->setColumnValueAndWidth('AU',$row,"Transfer FIRMS");
        $this->setColumnValueAndWidth('AV',$row,"Inbond Control Number");
        $this->setColumnValueAndWidth('AW',$row,"FDA Indicator");
        $this->setColumnValueAndWidth('AX',$row,"ACAS Filing");
        $this->setColumnValueAndWidth('AY',$row,"IncludeType86");
        $this->setColumnValueAndWidth('AZ',$row,"Entry Type");

        $this->setColumnValueAndWidth('BA',$row,"T86 Date of Arrival");
        $this->setColumnValueAndWidth('BB',$row,"IOR Type");
        $this->setColumnValueAndWidth('BC',$row,"IOR Number");
        $this->setColumnValueAndWidth('BD',$row,"Mode of Transport");
        $this->setColumnValueAndWidth('BE',$row,"Bond Type");
        $this->setColumnValueAndWidth('BF',$row,"Cargo Location FIRMS");
        $this->setColumnValueAndWidth('BG',$row,"Surety Code");
        $this->setColumnValueAndWidth('BH',$row,"Bond Amount");
        $this->setColumnValueAndWidth('BI',$row,"Express Consigment Shipment");
        $this->setColumnValueAndWidth('BJ',$row,"Known Importer");
        $this->setColumnValueAndWidth('BK',$row,"Perishable Goods");
        $this->setColumnValueAndWidth('BL',$row,"Additional Reference Code A");
        $this->setColumnValueAndWidth('BM',$row,"Additional Reference Number A");
        $this->setColumnValueAndWidth('BN',$row,"Additional Reference Code B");
        $this->setColumnValueAndWidth('BO',$row,"Additional Reference Number B");
        $this->setColumnValueAndWidth('BP',$row,"Port of Entry");
        $this->setColumnValueAndWidth('BQ',$row,"Equipment Number");

        $this->currentRow++;    
    }
    public function setColumnValueAndWidth($column,$row,$value,$width=30)
    {
        $this->setColumnWidth($column, $width);
         $this->setCellValue($column.$row, $value);  
    }
}
