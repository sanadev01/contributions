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
            $this->setCellValue('C'.$row, $order[0]->getUspsResponse()->total_amount);
            $this->setCellValue('D'.$row, $order[0]->usps_cost);
            $this->setCellValue('E'.$row, $order[0]->getUspsResponse()->usps->mail_class);
            $this->setCellValue('F'.$row, $order->count());
            $this->setCellValue('G'.$row, $order[0]->getUspsResponse()->weight.' '.$order[0]->getUspsResponse()->weight_unit);
            $this->setCellValue('H'.$row, $order[0]->getUspsResponse()->from_address->postal_code);
            $this->setCellValue('I'.$row, $order[0]->getUspsResponse()->to_address->postal_code);
            
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
        $this->setCellValue('C1', 'Paid To USPS (USD)');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'Charged From Customer (USD)');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'Service');

        $this->setColumnWidth('F', 20);
        $this->setCellValue('F1', 'Pieces');

        $this->setColumnWidth('G', 20);
        $this->setCellValue('G1', 'Weight');
        
        $this->setColumnWidth('H', 20);
        $this->setCellValue('H1', 'ZipCode Origin');

        $this->setColumnWidth('I', 20);
        $this->setCellValue('I1', 'ZipCode Destination');
        
        $this->setBackgroundColor('A1:I1', '2b5cab');
        $this->setColor('A1:I1', 'FFFFFF');

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
