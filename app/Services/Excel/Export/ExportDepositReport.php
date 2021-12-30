<?php

namespace App\Services\Excel\Export;

use App\Models\ShippingService;
use Illuminate\Support\Collection;

class ExportDepositReport extends AbstractExportService
{
    private $deposits;

    private $currentRow = 1;

    public function __construct(Collection $deposits)
    {
        $this->deposits = $deposits;

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

        foreach ($this->deposits as $deposit) {

            $this->setCellValue('A'.$row, $deposit->uuid);
            $this->setCellValue('B'.$row, optional($deposit->getOrder($deposit->order_id))->warehouse_number);
            $this->setCellValue('C'.$row, optional(optional($deposit->getOrder($deposit->order_id))->recipient)->fullName());
            $this->setCellValue('D'.$row, optional($deposit->getOrder($deposit->order_id))->customer_reference);
            $this->setCellValue('E'.$row, ($deposit->firstOrder()) ? optional($deposit->firstOrder())->us_api_tracking_code : optional($deposit->getOrder($deposit->order_id))->corrios_tracking_code);
            $this->setCellValue('F'.$row, $deposit->created_at->format('m/d/Y'));
            $this->setCellValue('G'.$row, $deposit->amount);
            $this->setCellValue('H'.$row, ($deposit->getOrder($deposit->order_id)) ? $this->getShippingCarrier($deposit ,$deposit->getOrder($deposit->order_id)) : '');
            $this->setCellValue('I'.$row, $deposit->isCredit() ? 'Credit' : 'Debit');
            $row++;
        }

        $this->currentRow = $row;
    }

    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'DP#');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'WHR#');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'Recipient');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'Customer Reference#');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'Tracking Code');

        $this->setColumnWidth('F', 20);
        $this->setCellValue('F1', 'Date');

        $this->setColumnWidth('G', 20);
        $this->setCellValue('G1', 'Amount');

        $this->setColumnWidth('H', 20);
        $this->setCellValue('H1', 'Carrier');

        $this->setColumnWidth('I', 20);
        $this->setCellValue('I1', 'Credit/Debit');

        $this->setBackgroundColor('A1:I1', '2b5cab');
        $this->setColor('A1:I1', 'FFFFFF');

        $this->currentRow++;
    }

    private function getShippingCarrier($deposit, $order)
    {
        if ($deposit->firstOrder() && $deposit->firstOrder()->hasSecondLabel()) {
            return ($deposit->firstOrder()->us_api_service == ShippingService::UPS_GROUND) ? 'UPS' : 'USPS';
        }
       return optional($order->shippingService)->name;
    }
}
