<?php

namespace App\Services\Excel\Export;
use App\Models\Order;
use App\Models\ShippingService;
use App\Models\Warehouse\AccrualRate;
use App\Models\Warehouse\Container;
use App\Models\Warehouse\DeliveryBill;

class ExportWhiteLabelManifest extends AbstractExportService
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
    public function superHeadingRow()
    {
    $this->mergeCells('A1:Q5');
        $this->sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        $this->sheet->getStyle('A1')->getAlignment()->setVertical('center');
        $this->setCellValue('A1' ,'MANIFESTO DE EXPORTAÇÃO REMESSA EXPRESSA');
        $this->sheet->getStyle('A1')->applyFromArray(
             [
            'font' => [
                'bold' => true,
                'color' => [
                    'rgb' => '000000'
                ],
                'size' => 13,
                'name' => 'Verdana'
            ]
        ]
        );
        $this->currentRow= 6;
    }
    private function prepareExcelSheet()
    {
        $this->superHeadingRow();
        $this->setExcelHeaderRow();        
        $row = $this->currentRow;
        foreach ($this->deliveryBill->containers as $container){
        foreach ($container->orders as $package) {
                $this->setCellValue('A'.$row, $package->corrios_tracking_code);
                $this->setCellValue('B'.$row, $this->date);
                $this->setCellValue('C'.$row, $package->getSenderFullName());
                $this->setCellValue('D'.$row,($package->recipient)->getRecipientInfo());
                $this->setCellValue('E'.$row,($package->recipient)->getAddress());
                $this->setCellValue('F'.$row, 1);
                $this->setCellValue('G'.$row, $package->getOriginalWeight('kg'));
                $this->setCellValue('H'.$row,   $package->getWeight('kg'));
                $this->setCellValue('I'.$row,'contents');
                $this->setCellValue('J'.$row,'ncm' );
                $this->setCellValue('K'.$row,  $package->getOrderValue());
                $this->setCellValue('L'.$row, $package->warehouse_number);
                $this->setCellValue('M'.$row,   $package->gross_total);
                $this->setCellValue('N'.$row, $container->getDestinationAriport());
                $this->setCellValue('O'.$row,$this->getValuePaidToCorrieos($container,$package)['airport']);
                $this->setCellValue('P'.$row, $this->getValuePaidToCorrieos($container,$package)['commission']); 
                $this->setCellValue('Q'.$row,  optional($package->affiliateSale)->commission);
             foreach ($package->items as $item) {
                    $this->setCellValue('I'.$row, $item->description);
                    $this->setCellValue('J'.$row, $item->sh_code);
                    $row++;
                }
                $row++;
                $this->totalCustomerPaid +=  $package->gross_total;
                $this->totalPaidToCorreios += $this->getValuePaidToCorrieos($container,$package)['airport'];
                $this->totalPieces++;
                $this->totalWeight += $package->getOriginalWeight('kg');
                $this->totalCommission += optional($package->affiliateSale)->commission;
                $this->totalAnjunCommission += $this->getValuePaidToCorrieos($container,$package)['commission'];
            }
        }
        $this->setCellValue('E'.$row, "Total");
        $this->setCellValue('F'.$row, $this->totalPieces);
        $this->setCellValue('G'.$row, $this->totalWeight);
        $this->setCellValue('M'.$row, $this->totalCustomerPaid);     
        $this->setCellValue('O'.$row, $this->totalPaidToCorreios);
        $this->setCellValue('P'.$row, $this->totalAnjunCommission);
        $this->setCellValue('Q'.$row, $this->totalCommission);
        $this->currentRow = $row;
    }
    private function setExcelHeaderRow()
    {

        foreach (range('A', 'Q') as $char) {
            $this->mergeCells($char.$this->currentRow.':'.$char.($this->currentRow+2)); 
        }

        $this->sheet->getStyle('A'.$this->currentRow.':Q'.$this->currentRow)->getAlignment()->setHorizontal('center');
        $this->sheet->getStyle('A'.$this->currentRow.':Q'.$this->currentRow)->getAlignment()->setVertical('center'); 
        $this->sheet->getStyle('A'.$this->currentRow.':Q'.$this->currentRow)->applyFromArray(
             [
            'font' => [
                'bold' => false,
                'color' => [
                    'rgb' => 'fff'
                ],
                'size' => 8,
                'name' => 'Verdana'
            ]
        ]
        );

        $this->setColumnWidth('A', 20);
        $this->setCellValue('A'.$this->currentRow, "Customer Reference");

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B'.$this->currentRow, "Tracking");

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C'.$this->currentRow, "CPF or CNPJ");

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D'.$this->currentRow, "Sender Name");

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E'.$this->currentRow, "Sender Address");

        $this->setColumnWidth('F', 20);
        $this->setCellValue('F'.$this->currentRow, "Address Complement");

        $this->setColumnWidth('G', 23);
        $this->setCellValue('G'.$this->currentRow, "Zip code");

        $this->setColumnWidth('H', 25);
        $this->setCellValue('H'.$this->currentRow, "Quantity of Volumes");

        $this->setColumnWidth('I', 25);
        $this->setCellValue('I'.$this->currentRow, "Gross Weight");
        
        $this->setColumnWidth('J', 25);
        $this->setCellValue('J'.$this->currentRow, "Value of Goods");
  
        $this->setColumnWidth('K', 20);
        $this->setCellValue('K'.$this->currentRow, "Description of Goods");
        
        $this->setColumnWidth('L', 20);
        $this->setCellValue('L'.$this->currentRow, "Country of Destination");

        $this->setColumnWidth('M', 20);
        $this->setCellValue('M'.$this->currentRow, "Buyer NAme");
        
        $this->setColumnWidth('N', 20);
        $this->setCellValue('N'.$this->currentRow, "TAX ID Buyer");

        $this->setColumnWidth('O', 20);
        $this->sheet->getStyle('O')->getAlignment()->setHorizontal('center');
        $this->setCellValue('O'.$this->currentRow, "Buyer Address");

        $this->setColumnWidth('P', 20);
        $this->setCellValue('P'.$this->currentRow, "Address complement");

        $this->setColumnWidth('Q', 20);
        $this->setCellValue('Q'.$this->currentRow, "Remarks"); 
 
        
        $this->setBackgroundColor('A'.$this->currentRow.':Q'.$this->currentRow, "f2f2f2");
        $this->setColor('A'.$this->currentRow.':Q'.$this->currentRow, "000");
        $this->currentRow= $this->currentRow+3;
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