<?php

namespace App\Services\Excel\Export;

use App\Models\User;

class ExportTracking extends AbstractExportService
{
    private $trackingData;
    private $currentRow = 1;

    public function __construct($trackingData)
    {
        $this->trackingData = $trackingData;
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

        foreach($this->trackingData as $data){

            if ($data['service'] == 'HD') {
                $trackingDescription = last($data['trackings'])['description'];
            }elseif ($data['service'] == 'Correios_Brazil') {
                $trackingDescription = $data['api_trackings']['descricao'];
            }elseif ($data['service'] == 'Correios_Chile') {
                $trackingDescription = $data['api_trackings']['Estado'];
            }elseif ($data['service'] == 'UPS') {
                $trackingDescription = $data['api_trackings']['status']['description'];
            }elseif ($data['service'] == 'PostNL') {

                if($data['api_trackings']['status'] == '1233' || $data['api_trackings']['status'] == '3') {
                    $trackingDescription = "The item is received";
                }elseif($data['api_trackings']['status'] == '38'){
                    $trackingDescription = "The item is released by customs";
                }elseif($data['api_trackings']['status'] == '1239' || $data['api_trackings']['status'] == '1240'){
                    $trackingDescription = "The item is in transit";
                }elseif($data['api_trackings']['status'] == '74'){
                    $trackingDescription = "The item is out for delivery";
                } else{
                    $trackingDescription = "Item Delivered";
                }
            }

            $user = User::find($data['order']['user_id']);

            $this->setCellValue('A'.$row, date('d-m-Y', strtotime($data['order']['order_date'])));
            $this->setCellValue('B'.$row, $data['order']['warehouse_number']);
            $this->setCellValue('C'.$row, $user->name);
            $this->setCellValue('D'.$row, $data['order']['merchant']);
            $this->setCellValue('E'.$row, $data['order']['corrios_tracking_code']);
            $this->setCellValue('F'.$row, $trackingDescription);

            $row++;
        }
    }

    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'Date');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'WareHouse#');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'Name');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'Loja/Cliente');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'Tracking Code');

        $this->setColumnWidth('F', 30);
        $this->setCellValue('F1', 'Tracking Status');

        $this->currentRow++;
    }
}
