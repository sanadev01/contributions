<?php

namespace App\Services\Excel\Export;
use Illuminate\Support\Collection;
use App\Models\AffiliateSale;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ProductExport extends AbstractExportService
{
    private $products;

    private $currentRow = 1;

    public function __construct(Collection $products)
    {
        $this->products = $products;

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

        foreach ($this->products as $product) {
            $user = $product->user;
        
            $this->setCellValue('A'.$row, $user->name);
            $this->setCellValue('B'.$row, $product->name);
            $this->setCellValue('C'.$row, $product->price);
            $this->setCellValue('D'.$row, $product->sku);
            $this->setCellValue('E'.$row, $product->status);
            $this->setCellValue('F'.$row, $product->description);
            $this->setCellValue('G'.$row, $product->created_at->format('m/d/Y'));
            
            $row++;
        }

       
    }


    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'User');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'Name');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'Price');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'SKU');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'Status');

        $this->setColumnWidth('F', 20);
        $this->setCellValue('F1', 'Description');

        $this->setColumnWidth('G', 20);
        $this->setCellValue('G1', 'Date');

        $this->setBackgroundColor('A1:G1', '2b5cab');
        $this->setColor('A1:G1', 'FFFFFF');

        $this->currentRow++;
    }
}
