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
            $this->setCellValue('G'.$row, $product->order);
            $this->setCellValue('H'.$row, $product->category);
            $this->setCellValue('I'.$row, $product->brand);
            $this->setCellValue('J'.$row, $product->manufacturer);
            $this->setCellValue('K'.$row, $product->barcode);
            $this->setCellValue('L'.$row, $product->quantity);
            $this->setCellValue('M'.$row, $product->item);
            $this->setCellValue('N'.$row, $product->lot);
            $this->setCellValue('O'.$row, $product->unit);
            $this->setCellValue('P'.$row, $product->case);
            $this->setCellValue('Q'.$row, $product->inventory_value);
            $this->setCellValue('R'.$row, $product->min_quantity);
            $this->setCellValue('S'.$row, $product->max_quantity);
            $this->setCellValue('T'.$row, $product->discontinued);
            $this->setCellValue('U'.$row, $product->store_day);
            $this->setCellValue('V'.$row, $product->location);
            $this->setCellValue('W'.$row, $product->sh_code);
            $this->setCellValue('X'.$row, date('d-m-Y', strtotime($product->created_at)));
            $this->setCellValue('Y'.$row, $product->exp_date? date('d-m-Y', strtotime($product->exp_date)): '');
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
        $this->setCellValue('G1', 'Order');

        $this->setColumnWidth('H', 20);
        $this->setCellValue('H1', 'Category');

        $this->setColumnWidth('I', 20);
        $this->setCellValue('I1', 'Brand');

        $this->setColumnWidth('J', 20);
        $this->setCellValue('J1', 'Manufacturer');

        $this->setColumnWidth('K', 20);
        $this->setCellValue('K1', 'Barcode');

        $this->setColumnWidth('L', 20);
        $this->setCellValue('L1', 'Quantity');

        $this->setColumnWidth('M', 20);
        $this->setCellValue('M1', 'Item');

        $this->setColumnWidth('N', 20);
        $this->setCellValue('N1', 'Lot');

        $this->setColumnWidth('O', 20);
        $this->setCellValue('O1', 'Unit');

        $this->setColumnWidth('P', 20);
        $this->setCellValue('P1', 'Case');

        $this->setColumnWidth('Q', 20);
        $this->setCellValue('Q1', 'Inventory_value');

        $this->setColumnWidth('R', 20);
        $this->setCellValue('R1', 'Min Quantity');

        $this->setColumnWidth('S', 20);
        $this->setCellValue('S1', 'Max Quantity');

        $this->setColumnWidth('T', 20);
        $this->setCellValue('T1', 'Discontinued');

        $this->setColumnWidth('U', 20);
        $this->setCellValue('U1', 'Store Day');

        $this->setColumnWidth('V', 20);
        $this->setCellValue('V1', 'Location');

        $this->setColumnWidth('W', 20);
        $this->setCellValue('W1', 'SH Code');
        
        $this->setColumnWidth('X', 20);
        $this->sheet->getStyle('X')->getAlignment()->setHorizontal('right');
        $this->setCellValue('X1', 'Created At');
        
        $this->setColumnWidth('Y', 20);
        $this->setCellValue('Y1', 'Expiry Date');

        $this->setBackgroundColor('A1:Y1', '2b5cab');
        $this->setColor('A1:Y1', 'FFFFFF');

        $this->currentRow++;
    }
}
