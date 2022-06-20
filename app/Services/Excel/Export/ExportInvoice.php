<?php

namespace App\Services\Excel\Export;

use function round;
use App\Models\Order;
use function strip_tags;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ExportInvoice extends AbstractExportService
{
    private $order;
    private $invoice;
    private $address;
    private $user;

    private $currentRow = 1;

    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->invoice = $order->invoice;
        $this->address = $order->address;
        $this->user = $order->user;

        parent::__construct();
    }

    public function handle()
    {
        $this->prepareExcelSheet();

        return $this->download();
    }

    private function prepareExcelSheet()
    {
        $this->prePareTitle();
        $this->setFromAddress();
        $this->setToAddress();
        $this->setItemsHeader();

        $row = 10;
        foreach ($this->invoice->items as $item) {
            $this->mergeCells("A{$row}:E{$row}");
            $this->setCellValue("A{$row}", $item->description);
            $this->mergeCells("F{$row}:G{$row}");
            $this->setCellValue("F{$row}", $item->qty);
            $this->mergeCells("H{$row}:I{$row}");
            $this->setCellValue("H{$row}", $item->ncm);
            $this->setCellValue("I{$row}", $item->value);
            $this->setCellValue("J{$row}", $item->value * $item->qty);
            $row++;
        }

        $this->currentRow = $row;

        $this->setTotalLine();
        $this->setFreightLine();
    }

    private function prePareTitle()
    {
        $this->mergeCells('A1:L1');
        $this->setRowHeight($this->currentRow, 35);
        $this->setCellValue('A1', 'Invoice');
        $this->setBold('A1', true);
        $this->setAlignment('A1', Alignment::VERTICAL_CENTER);
        $this->setAlignment('A1', Alignment::HORIZONTAL_CENTER);
        $this->setFontSize('A1', 22);
    }

    public function setFromAddress()
    {
        $this->setCellValue('A2', 'DE');
        $this->mergeCells('A3:C3');
        $this->setCellValue('A3', '');
        $this->mergeCells('A4:C6');
        $address = strip_tags($this->user->poBox->getCompleteAddress());
        $this->setCellValue('A4', "{$this->invoice->getSenderName()}\r\n{$address}}");
        $this->setAlignment('A4', Alignment::VERTICAL_TOP);
        $this->setTextWrap('A4', true);
        $this->setRowHeight(4, 58);
    }

    public function setToAddress()
    {
        $this->setCellValue('F2', 'para');
        $this->mergeCells('F3:J3');
        $this->setCellValue('F3', '');
        $this->mergeCells('F4:J6');
        $this->setCellValue(
            'F4',
            "WHR#:{$this->order->shipment->whr_number}\r\n {$this->address->name}\r\n{$this->address->address}, {$this->address->address2}\r\n{$this->address->city},{$this->address->uf},{$this->address->country->name},\r\n CEP:{$this->address->cep} \r\nCPF/CNPJ:".($this->address->isBusiness() ? $this->address->cnpj : $this->address->cpf)."\r\nEmail: {$this->address->email} \r\n Phone: {$this->address->phone}"
        );
        $this->setAlignment('F4', Alignment::VERTICAL_TOP);
        $this->setTextWrap('F4', true);
        $this->setRowHeight(4, 90);
    }

    public function setItemsHeader()
    {
        $this->setRowHeight(9, 30);
        $this->setBold('A9:J9', true);
        $this->mergeCells('A9:E9');
        $this->setCellValue('A9', 'Descrição');
        $this->mergeCells('F9:G9');
        $this->setCellValue('F9', 'Quantidade');
        $this->mergeCells('H9:I9');
        $this->setCellValue('H9', 'NCM/Harmonized Code');
        $this->setTextWrap('H9', true);
        $this->setCellValue('I9', 'Valor Unitario');
        $this->setCellValue('J9', 'Total');
    }

    public function setTotalLine()
    {
        $this->setBackgroundColor("A{$this->currentRow}:J{$this->currentRow}", '808080');
        $this->setColor("A{$this->currentRow}:J{$this->currentRow}", 'FFFFFF');
        $this->mergeCells("A{$this->currentRow}:I{$this->currentRow}");
        $this->setCellValue("A{$this->currentRow}", 'Total');
        $lastCell = $this->currentRow - 1;
        $this->setCellValue("J{$this->currentRow}", "=SUM(J10:J{$lastCell})");
        $this->setAlignment("A{$this->currentRow}:I{$this->currentRow}", Alignment::HORIZONTAL_LEFT);
        $this->setAlignment("A{$this->currentRow}", Alignment::HORIZONTAL_RIGHT);
        $this->currentRow++;
    }

    public function setFreightLine()
    {
        $this->setCellValue("A{$this->currentRow}", 'Freight Charges');
        $this->setBold("A{$this->currentRow}", true);
        $this->setCellValue("J{$this->currentRow}", round($this->invoice->freight));
        $this->setBold("J{$this->currentRow}", true);

        $this->sheet->getStyle("J10:J{$this->currentRow}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_ACCOUNTING_USD);
    }
}
