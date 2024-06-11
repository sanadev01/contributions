<?php

namespace App\Services\Excel\Export;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ContainerOrderExport extends AbstractExportService
{
    private $orders;

    private $currentRow = 2;

    public function __construct(Collection $orders)
    {
        $this->orders = $orders;

        parent::__construct();
    }

    public function handle()
    {
        $this->prepareExcelSheet();

        return $this->download();
    }

    private function prepareExcelSheet()
    {
        $this->setExcelHeaderRow();

        $row = $this->currentRow;
        


        foreach ($this->orders as $order) {

            $this->setCellFormat('A'.$row, '#');
            $this->setAlignment('A'.$row, 'left');
            $this->setCellValue('A'.$row, (string)$order->corrios_tracking_code);
            $this->setCellValue('B'.$row, $order->warehouse_number);
            $this->setCellValue('C'.$row, $order->getOriginalWeight('kg'));
            $this->setCellValue('D'.$row, $order->getWeight('lbs').' lbs / '. $order->getWeight('kg'). ' kg');
            $this->setCellValue('E'.$row, optional($order->user)->pobox_number.' / '.optional($order->user)->getFullName());
            $this->setCellValue('F'.$row, $order->getSenderFullName());
            $this->setCellValue('G'.$row, $order->customer_reference);
            
            $row++;
        }

        $this->currentRow = $row;
        
    }


    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'Packages Inside Container');
       
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A2', 'Tracking Code');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B2', 'WHR#');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C2', 'Weight');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D2', 'Volume Weight');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E2', 'POBOX#');

        $this->setColumnWidth('F', 20);
        $this->setCellValue('F2', 'Sender');
        
        $this->setColumnWidth('G', 20);
        $this->setCellValue('G2', 'Customer Reference');
        
        $this->setBackgroundColor('A2:G2', '2b5cab');
        $this->setColor('A2:G2', 'FFFFFF');
        $this->mergeCells('A1:G1');

        $this->setAlignment('A1', Alignment::VERTICAL_CENTER);
        $this->setBackgroundColor('A1', 'b1afaf');
        $this->setBold('A1', true);
        $this->setFontSize('A1', 18);

        $this->currentRow++;
    }
}
