<?php

namespace App\Services\Excel\Export;
use App\Models\AffiliateSale;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SaleExport extends AbstractExportService
{
    private $sales;

    private $currentRow = 1;

    public function __construct(Collection $sales)
    {
        $this->sales = $sales;

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

        foreach ($this->sales as $sale) {
            $user = $sale->user;
            $commissionUser = $sale->order->user;
            if ( Auth::user()->isAdmin() ){
                $this->setCellValue('A'.$row, $user->name . $user->pobox_number);
            }
            $this->setCellValue('B'.$row, optional($commissionUser)->name . optional($commissionUser)->pobox_number);
            $this->setCellValue('C'.$row, 'HD-'.$sale->order_id);
            $this->setCellValue('D'.$row, $sale->order->corrios_tracking_code);
            $this->setCellValue('E'.$row, $sale->order->customer_reference);
            $this->setCellValue('F'.$row, $sale->type);
            $this->setCellValue('G'.$row, number_format($sale->value, 2));
            $this->setCellValue('H'.$row, number_format($sale->commission, 2));
            $this->setCellValue('I'.$row, $sale->is_paid? 'paid': 'unpaid');
            $this->setCellValue('J'.$row, $sale->created_at->format('m/d/Y'));
            
            $row++;
        }

        $this->currentRow = $row;

        $this->setCellValue('D'.$row, "=SUM(D1:D{$row})");
        $this->setBackgroundColor("A{$row}:H{$row}", 'adfb84');
    }


    private function setExcelHeaderRow()
    {
        if ( Auth::user()->isAdmin() ){
            $this->setColumnWidth('A', 20);
            $this->setCellValue('A1', 'Name');
        }
        
        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'Commission From');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'WHR#');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'Tracking Code');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'Customer Ref#');
        
        $this->setColumnWidth('F', 20);
        $this->setCellValue('F1', 'Type');
        
        $this->setColumnWidth('G', 20);
        $this->setCellValue('G1', 'Commission Value');
        
        $this->setColumnWidth('H', 20);
        $this->setCellValue('H1', 'Commission');
        
        $this->setColumnWidth('I', 20);
        $this->setCellValue('I1', 'Paid/unpaid');

        $this->setColumnWidth('J', 20);
        $this->setCellValue('J1', 'Date');

        $this->setBackgroundColor('A1:J1', '2b5cab');
        $this->setColor('A1:J1', 'FFFFFF');

        $this->currentRow++;
    }
}
