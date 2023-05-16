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
        $this->mergeCells('A1:V5');
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
                $this->setCellValue('R'.$row,  optional(optional($package->affiliateSale)->user)->pobox_number.' '.optional(optional($package->affiliateSale)->user)->name);
                $this->setCellValue('S'.$row,  $container->dispatch_number);
                $this->setCellValue('T'.$row, optional($package->user)->pobox_number.' / '.optional($package->user)->getFullName());
                $this->setCellValue('U'.$row,  $package->tracking_id);
                $this->setCellValue('V'.$row,  setting('marketplace_checked', null, $package->user->id)?  setting('marketplace', null, $package->user->id):'');
                foreach ($package->items as $item) {
                    $this->setCellValue('I'.$row, $item->description);
                    $this->setCellValue('J'.$row, $item->sh_code);
                    $this->row++;
                }
                $this->row++;
                $this->totalCustomerPaid +=  $package->gross_total;
                $this->totalPaidToCorreios += $this->getValuePaidToCorrieos($container,$package)['airport'];
                $this->totalPieces++;
                $this->totalWeight += $package->getOriginalWeight('kg');
                $this->totalCommission += optional($package->affiliateSale)->commission;
                $this->totalAnjunCommission += $this->getValuePaidToCorrieos($container,$package)['commission'];
            }
            $row++;
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

        foreach (range('A', 'V') as $char) {
            $this->mergeCells($char.$this->currentRow.':'.$char.($this->currentRow+2)); 
        }

        $this->sheet->getStyle('A'.$this->currentRow.':V'.$this->currentRow)->getAlignment()->setHorizontal('center');
        $this->sheet->getStyle('A'.$this->currentRow.':V'.$this->currentRow)->getAlignment()->setVertical('center'); 
        $this->sheet->getStyle('A'.$this->currentRow.':V'.$this->currentRow)->applyFromArray(
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
        $this->setCellValue('A'.$this->currentRow, "CÓDIGO INTERNO \n CLIENTE \n (se houver)");

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B'.$this->currentRow, "Nº REMESSA \n (11 a 16 caracteres)");

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C'.$this->currentRow, "CPF/CNPJ/ \n PASSAPORTE \n (REMETENTE)");

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D'.$this->currentRow, "NOME DO REMETENTE");

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E'.$this->currentRow, "ENDEREÇO  \n REMETENTE \n  (Logadouro e número)");

        $this->setColumnWidth('F', 20);
        $this->setCellValue('F'.$this->currentRow, "COMPLEMENTO \n ENDEREÇO \n REMETENTE");

        $this->setColumnWidth('G', 23);
        $this->setCellValue('G'.$this->currentRow, "CEP \n REMETENTE");

        $this->setColumnWidth('H', 25);
        $this->setCellValue('H'.$this->currentRow, "QTDE. VOLUMES");

        $this->setColumnWidth('I', 25);
        $this->setCellValue('I'.$this->currentRow, "PESO BRUTO \nKG");
        
        $this->setColumnWidth('J', 25);
        $this->setCellValue('J'.$this->currentRow, "VALOR DA \n REMESSA \n USD");
  
        $this->setColumnWidth('K', 20);
        $this->setCellValue('K'.$this->currentRow, "DESCRIÇÃO DA \n REMESSA \n (com quntidade)");
        
        $this->setColumnWidth('L', 20);
        $this->setCellValue('L'.$this->currentRow, "PAÍS DE DESTINO");

        $this->setColumnWidth('M', 20);
        $this->setCellValue('M'.$this->currentRow, "NOME \n DESTINATÁRIO");
        
        $this->setColumnWidth('N', 20);
        $this->setCellValue('N'.$this->currentRow, "TAX ID \n DESTINATÁRIO");

        $this->setColumnWidth('O', 20);
        $this->sheet->getStyle('O')->getAlignment()->setHorizontal('center');
        $this->setCellValue('O'.$this->currentRow, "ENDEREÇO \n DESTINATARIO");

        $this->setColumnWidth('P', 20);
        $this->setCellValue('P'.$this->currentRow, "COMPOLEMENTO \n ENDEREÇO \n DESTINATÁRIO");

        $this->setColumnWidth('Q', 20);
        $this->setCellValue('Q'.$this->currentRow, "OBSERVAÇÕES"); 

        $this->setColumnWidth('R', 20);
        $this->setCellValue('R'.$this->currentRow, 'Commission Paid to');

        $this->setColumnWidth('S', 20);
        $this->setCellValue('S'.$this->currentRow, 'Bag');
        
        $this->setColumnWidth('T', 20);
        $this->setCellValue('T'.$this->currentRow, 'POBOX / NAME');
        $this->setColumnWidth('U', 20);
        $this->setCellValue('U'.$this->currentRow, 'Carrier Tracking');
        $this->setColumnWidth('V', 20);
        $this->setCellValue('V'.$this->currentRow, 'Marketplace');
        
        $this->setBackgroundColor('A'.$this->currentRow.':V'.$this->currentRow, "f2f2f2");
        $this->setColor('A'.$this->currentRow.':W'.$this->currentRow, "000");
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
