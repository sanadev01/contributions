<?php

namespace App\Services\CorreosChile;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ExportExcelChileManifestService extends AbstractExportService
{
    protected $container;

    private $currentRow = 2;

    public function __construct($container)
    {
        $this->container = $container;

        parent::__construct($this->container);

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
        
        $orders = $this->container->orders;

        foreach ($orders as $order) {

            $chile_response = json_decode($order->chile_response);
            $this->setCellValue('A'.$row, $this->combineChileResponseFields($chile_response));
            $this->setCellValue('B'.$row, $this->container->seal_no);
            $this->setCellValue('C'.$row, 'LS1293842224');

            $row++;
        }

        $this->currentRow = $row;
        
    }


    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'Correos Chile Container Menifest');
       
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A2', ' Tracking Number');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B2', 'AWB Number');
        
        $this->setColumnWidth('C', 20);
        $this->setCellValue('C2', 'REF NUMBER');

        $this->setBackgroundColor('A2:C2', '2b5cab');
        $this->setColor('A2:C2', 'FFFFFF');
        $this->mergeCells('A1:C1');

        $this->setAlignment('A1', Alignment::VERTICAL_CENTER);
        $this->setBackgroundColor('A1', 'b1afaf');
        $this->setBold('A1', true);
        $this->setFontSize('A1', 18);

        $this->currentRow++;
    }

    private function combineChileResponseFields($chile_response)
    {
        return $chile_response->CodigoEncaminamiento.$chile_response->NumeroEnvio.'001';
    }
}
