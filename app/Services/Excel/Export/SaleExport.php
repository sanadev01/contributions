<?php

namespace App\Services\Excel\Export;
use Illuminate\Support\Collection;
use App\Models\AffiliateSale;
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
        
            $this->setCellValue('A'.$row, $user->name . $user->pobox_number);
            $this->setCellValue('B'.$row, 'HD-'.$sale->order_id);
            $this->setCellValue('C'.$row, number_format($sale->value, 2));
            $this->setCellValue('D'.$row, $sale->type);
            $this->setCellValue('E'.$row, $sale->order->corrios_tracking_code);
            $this->setCellValue('F'.$row, $sale->created_at->format('m/d/Y'));
            
            $row++;
        }

        $this->currentRow = $row;

        $this->setCellValue('E'.$row, "=SUM(E1:E{$row})");
        $this->setBackgroundColor("A{$row}:F{$row}", 'adfb84');
    }


    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'Name');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'WHR#');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'Commission Value');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'Type');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'Tracking Code');

        $this->setColumnWidth('F', 20);
        $this->setCellValue('F1', 'Date');

        $this->setBackgroundColor('A1:F1', '2b5cab');
        $this->setColor('A1:F1', 'FFFFFF');

        $this->currentRow++;
    }
}
