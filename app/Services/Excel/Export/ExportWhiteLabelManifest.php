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
    public function __construct(DeliveryBill $deliveryBill)
    {
        $this->deliveryBill = $deliveryBill;
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
        $this->setCellValue('A1', 'MANIFESTO DE EXPORTAÇÃO REMESSA EXPRESSA');
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
        $this->currentRow = 6;
    }
    private function prepareExcelSheet()
    {
        $this->superHeadingRow();
        $this->setExcelHeaderRow();
        $row = $this->currentRow;
        foreach ($this->deliveryBill->containers as $container) {
            foreach ($container->orders as $order) {
                $this->setCellValue('A' . $row, $order->customer_reference);
                $this->setCellValue('B' . $row, $order->corrios_tracking_code);
                $this->setCellValue('C' . $row, ' ' . $order->sender_taxId);
                $this->setCellValue('D' . $row, $order->getSenderFullName());
                $this->setCellValue('E' . $row, ($order->sender_address) ? $order->sender_address : '2200 NW 129TH AVE');
                $this->setCellValue('F' . $row, '');
                $this->setCellValue('G' . $row, ($order->sender_zipcode) ? $order->sender_zipcode : '33182');
                $this->setCellValue('H' . $row, count($order->items));
                $this->setCellValue('I' . $row, $order->getWeight('kg'));
                $this->setCellValue('J' . $row, $order->order_items_value);
                $this->setCellValue('L' . $row, $order->recipient->country->code);
                $this->setCellValue('M' . $row, $order->recipient->getFullName());
                $this->setCellValue('N' . $row, $order->recipient->tax_id);
                $this->setCellValue('O' . $row, $order->recipient->getAddress());
                $this->setCellValue('P' . $row, '');
                $this->setCellValue('Q' . $row, '');
                foreach ($order->items as $item) {
                    $this->setCellValue('K' . $row, $item->description);
                    $row++;
                }
                $row++;
            }
        }
        $this->currentRow = $row;
    }
    private function setExcelHeaderRow()
    {

        foreach (range('A', 'Q') as $char) {
            $this->mergeCells($char . $this->currentRow . ':' . $char . ($this->currentRow + 2));
        }

        $this->sheet->getStyle('A' . $this->currentRow . ':Q' . $this->currentRow)->getAlignment()->setHorizontal('center');
        $this->sheet->getStyle('A' . $this->currentRow . ':Q' . $this->currentRow)->getAlignment()->setVertical('center');
        $this->sheet->getStyle('A' . $this->currentRow . ':Q' . $this->currentRow)->applyFromArray(
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
        $this->setCellValue('A' . $this->currentRow, "Customer Reference");

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B' . $this->currentRow, "Tracking");

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C' . $this->currentRow, "CPF or CNPJ");

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D' . $this->currentRow, "Sender Name");

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E' . $this->currentRow, "Sender Address");

        $this->setColumnWidth('F', 20);
        $this->setCellValue('F' . $this->currentRow, "Address Complement");

        $this->setColumnWidth('G', 23);
        $this->setCellValue('G' . $this->currentRow, "Zip code");

        $this->setColumnWidth('H', 25);
        $this->setCellValue('H' . $this->currentRow, "Quantity of Volumes");

        $this->setColumnWidth('I', 25);
        $this->setCellValue('I' . $this->currentRow, "Gross Weight");

        $this->setColumnWidth('J', 25);
        $this->setCellValue('J' . $this->currentRow, "Value of Goods");

        $this->setColumnWidth('K', 20);
        $this->setCellValue('K' . $this->currentRow, "Description of Goods");

        $this->setColumnWidth('L', 20);
        $this->setCellValue('L' . $this->currentRow, "Country of Destination");

        $this->setColumnWidth('M', 20);
        $this->setCellValue('M' . $this->currentRow, "Buyer Name");

        $this->setColumnWidth('N', 20);
        $this->setCellValue('N' . $this->currentRow, "TAX ID Buyer");

        $this->setColumnWidth('O', 20);
        $this->sheet->getStyle('O')->getAlignment()->setHorizontal('center');
        $this->setCellValue('O' . $this->currentRow, "Buyer Address");

        $this->setColumnWidth('P', 20);
        $this->setCellValue('P' . $this->currentRow, "Address complement");

        $this->setColumnWidth('Q', 20);
        $this->setCellValue('Q' . $this->currentRow, "Remarks");


        $this->setBackgroundColor('A' . $this->currentRow . ':Q' . $this->currentRow, "f2f2f2");
        $this->setColor('A' . $this->currentRow . ':Q' . $this->currentRow, "000");
        $this->currentRow = $this->currentRow + 3;
    }
}
