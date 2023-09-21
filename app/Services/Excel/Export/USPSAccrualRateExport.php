<?php

namespace App\Services\Excel\Export;
use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class USPSAccrualRateExport extends AbstractExportService
{
    private $orders;

    private $currentRow = 1;

    private $count = 1;

    public function __construct($orders)
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

            $this->setCellValue('A'.$row, $order[0]->user->pobox_number);
            $this->setCellValue('B'.$row, $this->getPakagesWarehouse($order));
            $this->setCellValue('C'.$row, $order[0]->us_api_tracking_code);
            $this->setCellValue('D'.$row, $order[0]->getUSLabelResponse()->total_amount);
            $this->setCellValue('E'.$row, $order[0]->us_api_cost);
            $this->setCellValue('F'.$row, $order[0]->getUSLabelResponse()->usps->mail_class);
            $this->setCellValue('G'.$row, $order->count());
            $this->setCellValue('H'.$row, $order[0]->getUSLabelResponse()->weight.' '.$order[0]->getUSLabelResponse()->weight_unit);
            $this->setCellValue('I'.$row, $order[0]->getUSLabelResponse()->from_address->postal_code);
            $this->setCellValue('J'.$row, $order[0]->getUSLabelResponse()->to_address->postal_code);
            
            $row++;
        }

        $this->currentRow = $row;
        
    }


    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'Customer ID');
       
        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'Order');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'Tracking Number');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'Paid To USPS (USD)');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'Charged From Customer (USD)');

        $this->setColumnWidth('F', 20);
        $this->setCellValue('F1', 'Service');

        $this->setColumnWidth('G', 20);
        $this->setCellValue('G1', 'Pieces');

        $this->setColumnWidth('H', 20);
        $this->setCellValue('H1', 'Weight');
        
        $this->setColumnWidth('I', 20);
        $this->setCellValue('I1', 'ZipCode Origin');

        $this->setColumnWidth('J', 20);
        $this->setCellValue('J1', 'ZipCode Destination');
        
        $this->setBackgroundColor('A1:J1', '2b5cab');
        $this->setColor('A1:J1', 'FFFFFF');

        $this->currentRow++;
    }

    private function getPakagesWarehouse($order)
    {
        $warehouse = '';

        foreach($order as $order)
        {
            $warehouse .= $order->warehouse_number . ',';
        }

        return $warehouse;
    }
}
