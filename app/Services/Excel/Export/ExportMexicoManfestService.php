<?php

namespace App\Services\Excel\Export;

use App\Services\Excel\Export\AbstractExportService;
use App\Models\Warehouse\DeliveryBill;
use App\Models\Warehouse\Container;

class ExportMexicoManfestService extends AbstractExportService
{
    private $currentRow = 1;
    private $deliveryBill;
    private $totalCustomerPaid = 0;

    public function __construct(DeliveryBill $deliveryBill)
    {
        $this->deliveryBill = $deliveryBill;
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
        foreach ($this->deliveryBill->containers as $container) {
            foreach ($container->orders as $order) {

                $tax = $order->recipient->tax_id;
                $taxLenght = strlen($tax);

                $this->setCellValue('A' . $row, $order->corrios_tracking_code);
                $this->setCellValue('B' . $row, '');
                $this->setCellValue('C' . $row, '');
                $this->setCellValue('D' . $row, $order->getSenderFullName());
                $this->setCellValue('E' . $row, $order->sender_address);
                $this->setCellValue('F' . $row, $order->sender_city);
                $this->setCellValue('G' . $row, $order->sender_city_zipcode);
                $this->setCellValue('H' . $row, optional($order->senderCountry)->name);
                $this->setCellValue('I' . $row, optional($order->senderCountry)->code);
                $this->setCellValue('J' . $row, $order->recipient->fullName());
                $this->setCellValue('K' . $row, $order->recipient->getAddress());
                $this->setCellValue('L' . $row, $order->recipient->city);
                $this->setCellValue('M' . $row, $order->recipient->zipcode);
                $this->setCellValue('N' . $row, $order->recipient->phone_number);
                $this->setCellValue('O' . $row, $order->recipient->country->code);
                $this->setCellValue('P' . $row, $taxLenght < 16 ? "$tax" : '');
                $this->setCellValue('Q' . $row, $taxLenght > 16 ? "$tax" : '');
                $this->setCellValue('R' . $row, $order->gross_total);
                $this->setCellValue('S' . $row, $order->weight);
                $this->setCellValue('T' . $row, $order->measurement_unit);
                $this->setCellValue('U' . $row, count($order->items));
                $this->setCellValue('V' . $row, 'USD');



                $this->totalCustomerPaid += $order->gross_total;


                foreach ($order->items as $item) {
                    $this->setCellValue('X' . $row, $item->description);
                    $this->setCellValue('X' . $row, $item->made_in);
                    $this->setCellValue('Y' . $row, $item->shCodeModel()->is_foot_wear ? 'Yes' : 'No');
                    $row++;
                }
            }
        }

        $this->setTotalRow($row);
    }

    private function setExcelHeaderRow()
    {

        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', ' MWB ');
        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'SACA');
        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'Tracking Number (AWB)');
        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'Shipper Name');
        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'Shipper Address');
        $this->setColumnWidth('F', 20);
        $this->setCellValue('F1', 'Shipper City');
        $this->setColumnWidth('G', 20);
        $this->setCellValue('G1', 'Shipper Zip Code');
        $this->setColumnWidth('H', 20);
        $this->setCellValue('H1', 'Shipper Country');
        $this->setColumnWidth('I', 20);
        $this->setCellValue('I1', 'Shipper Country Code');
        $this->setColumnWidth('J', 20);
        $this->setCellValue('J1', 'Consignee Name');
        $this->setColumnWidth('K', 20);
        $this->setCellValue('K1', 'Consignee Address');
        $this->setColumnWidth('L', 20);
        $this->setCellValue('L1', 'Consignee City');
        $this->setColumnWidth('M', 20);
        $this->setCellValue('M1', 'Consignee Zip Code');
        $this->setColumnWidth('N', 20);
        $this->setCellValue('N1', 'Consignee Phone');
        $this->setColumnWidth('O', 20);
        $this->setCellValue('O1', 'Consignee Country Code');

        $this->setColumnWidth('P', 20);
        $this->setCellValue('P1', 'RFC COGSIGNEE');
        $this->setColumnWidth('Q', 20);
        $this->setCellValue('Q1',  'CURP COGSIGNEE');
        $this->setColumnWidth('R', 20);
        $this->setCellValue('R1', 'Total Declared Value');
        $this->setColumnWidth('S', 20);
        $this->setCellValue('S1', 'PARCEL Weight');
        $this->setColumnWidth('T', 20);
        $this->setCellValue('T1', 'Weight UNIT');

        $this->setColumnWidth('U', 20);
        $this->setCellValue('U1', 'Total Items');
        $this->setColumnWidth('V', 20);
        $this->setCellValue('V1', 'Currency');
        $this->setColumnWidth('W', 20);
        $this->setCellValue('W1', 'Product Description');
        $this->setColumnWidth('X', 20);
        $this->setCellValue('X1', 'Product Origin');
        $this->setColumnWidth('Y', 20);
        $this->setCellValue('Y1', 'Footwear');

        $this->setBackgroundColor('A1:Y1', '2b5cab');
        $this->setColor('A1:Y1', 'FFFFFF');

        $this->currentRow++;
    }

    private function setTotalRow($row)
    {
        $this->setCellValue('Q' . $row, 'Total');
        $this->setCellValue('R' . $row, $this->totalCustomerPaid);
    }
}
