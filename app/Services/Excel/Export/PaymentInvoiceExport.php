<?php

namespace App\Services\Excel\Export;
use Illuminate\Support\Collection;
use App\Models\PaymentInvoice;
use Illuminate\Support\Facades\Auth;

class PaymentInvoiceExport extends AbstractExportService
{
    private $paymentInvoices;

    private $currentRow = 1;

    public function __construct(Collection $paymentInvoices)
    {
        $this->paymentInvoices = $paymentInvoices;
        
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
        
        foreach ($this->paymentInvoices as $paymentInvoice) {
            $user = $paymentInvoice->user;
            
            $this->setCellValue('A'.$row, $paymentInvoice->uuid);
            if ( Auth::user()->isAdmin() ){
                $this->setCellValue('B'.$row, $user->name);
            }
            $this->setCellValue('C'.$row, $paymentInvoice->order_count);
            $this->setCellValue('D'.$row, $paymentInvoice->total_amount);
            $this->setCellValue('E'.$row, $paymentInvoice->last_four_digits);
            $this->setCellValue('F'.$row, $paymentInvoice->is_paid ? 'Paid' : 'Pending');
            $this->setCellValue('G'.$row, $paymentInvoice->created_at);
            $row++;
        }

        $this->currentRow = $row;
    }

    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'Invoice #');
        
        if ( Auth::user()->isAdmin() ){
            $this->setColumnWidth('B', 20);
            $this->setCellValue('B1', 'Name');
        }

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'Orders Count');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'Amount');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'Card Last 4 Digits');

        $this->setColumnWidth('F', 20);
        $this->setCellValue('F1', '	Status');

        $this->setColumnWidth('G', 20);
        $this->setCellValue('G1', 'Created At');

        $this->setBackgroundColor('A1:G1', '2b5cab');
        $this->setColor('A1:G1', 'FFFFFF');

        $this->currentRow++;
    }
}
