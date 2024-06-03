<?php

namespace App\Services\CorreosChile;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Warehouse\Container;
use App\Models\Warehouse\AccrualRate;
use App\Models\Warehouse\DeliveryBill;
use App\Services\Excel\Export\AbstractExportService;


class ExportChileManifestService extends AbstractExportService
{
    private $container;
    private $currentRow = 1;
    private $count = 1;
    private $total_customerpaid;
    private $total_paid_to_correios;

    public function __construct($container)
    {
        $this->container = $container;
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

        foreach ($this->container->orders as $order) {
            $this->setCellFormat('A' . $row, '#');
            $this->setAlignment('A' . $row, 'left');
            $this->setCellValue('A' . $row, (string)$order->corrios_tracking_code);
            $this->setCellValue('B' . $row, Carbon::now()->format('m/d/Y'));
            $this->setCellValue('C' . $row, $order->getSenderFullName(),);
            $this->setCellValue('D' . $row, ($order->recipient)->getRecipientInfo());
            $this->setCellValue('E' . $row, ($order->recipient)->getAddress());
            $this->setCellValue('F' . $row, 1);
            $this->setCellValue('G' . $row, $order->getOriginalWeight('kg'));
            $this->setCellValue('H' . $row, $this->getOrderItemDescription($order));
            $this->setCellValue('I' . $row, $this->getOrderItemsSHCode($order));
            $this->setCellValue('J' . $row, $order->order_items_value);
            $this->setCellValue('K' . $row, $order->warehouse_number);
            $this->setCellValue('L' . $row, $order->gross_total);
            $this->setCellValue('M' . $row, $this->container->destination_ariport);
            $this->setCellValue('N' . $row, $this->getValuePaidToCorrieos($this->container, $order));
            $this->setCellValue('O' . $row, $this->container->dispatch_number);
            $this->setCellValue('P' . $row, optional($order->user)->pobox_number . ' / ' . optional($order->user)->getFullName());

            $this->total_customerpaid +=  $order->gross_total;
            $this->total_paid_to_correios += $this->getValuePaidToCorrieos($this->container, $order);

            $row++;
        }

        $this->currentRow = $row;
        $this->currentRow++;

        $this->setCellValue('K' . $this->currentRow, 'Total');
        $this->setCellValue('L' . $this->currentRow, $this->total_customerpaid);
        $this->setCellValue('N' . $this->currentRow, $this->total_paid_to_correios);
    }

    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'HAWB');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'Date');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'Shipper Name');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'ConsigneeName/CPF');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'ConsigneeAddres');

        $this->setColumnWidth('F', 20);
        $this->setCellValue('F1', 'Piece');

        $this->setColumnWidth('G', 20);
        $this->setCellValue('G1', 'Weigth');

        $this->setColumnWidth('H', 20);
        $this->setCellValue('H1', 'Contents');

        $this->setColumnWidth('I', 20);
        $this->setCellValue('I1', 'NCM');

        $this->setColumnWidth('J', 20);
        $this->setCellValue('J1', 'Value');

        $this->setColumnWidth('K', 20);
        $this->setCellValue('K1', 'WHR#');

        $this->setColumnWidth('L', 20);
        $this->setCellValue('L1', 'Customer paid');

        $this->setColumnWidth('M', 20);
        $this->setCellValue('M1', 'Airport/ GRU/CWB');

        $this->setColumnWidth('N', 20);
        $this->setCellValue('N1', 'Value paid to Correios');

        $this->setColumnWidth('O', 20);
        $this->setCellValue('O1', 'Bag');

        $this->setColumnWidth('P', 20);
        $this->setCellValue('P1', 'POBOX / NAME');

        $this->setBackgroundColor('A1:P1', '2b5cab');
        $this->setColor('A1:P1', 'FFFFFF');

        $this->currentRow++;
    }

    private function getOrderItemDescription($order)
    {
        foreach ($order->items as $item) {
            $itemDescription[] = $item->description;
        }

        $description = implode(" ", $itemDescription);

        return $description;
    }

    private function getOrderItemsSHCode($order)
    {
        foreach ($order->items as $item) {
            $itemSHCode[] = $item->sh_code;
        }

        $sh_code = implode(",", $itemSHCode);

        return $sh_code;
    }

    private function getValuePaidToCorrieos(Container $container, Order $order)
    {
        $service  = $order->shippingService->service_sub_class;
        $rateSlab = AccrualRate::getRateSlabFor($order->getWeight('kg'), $service);

        if (!$rateSlab) {
            return 0;
        }

        if ($container->destination_ariport ==  "Santiago") {
            return $rateSlab->gru;
        }

        return $rateSlab->cwb;
    }
}
