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
        $this->sales = $sales->sortByDesc('referrer_id');

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
        $checkUser = null;
        $sumRow = 'H1';
        $totalOrderRow = 'D1';
        foreach ($this->sales as $sale) {
            $user = $sale->user;
            $commissionUser = $sale->order->user; 
            $isReferrerNow = optional($commissionUser->referrer)->id==$user->id;

            if($checkUser && $checkUser != $commissionUser->pobox_number){
                $this->setCellValue('H'.$row, "Due Amount : ");
                $this->setCellValue('A'.$row, "Number Of Parcels Per Customer : ");
                $this->setCellValue('B'.$row, "=ROWS($totalOrderRow:D{$row})-2");
                $this->setAlignment('I'.$row,Alignment::HORIZONTAL_LEFT);
                $this->setAlignment('H'.$row,Alignment::HORIZONTAL_RIGHT);
                $this->setAlignment('B'.$row,Alignment::HORIZONTAL_LEFT);
                
                $this->setCellValue('I'.$row, "=SUM($sumRow:H{$row})");
                $this->setBackgroundColor("A{$row}:J{$row}", 'adfb84');
                $totalOrderRow = 'D'.$row;
                $row++;
                $sumRow = 'H'.$row;

            }
            
            if ( Auth::user()->isAdmin() ){
                if(!$isReferrerNow){
                    $this->setCellValue('K'.$row,'Referrer Removed');
                    $this->setBackgroundColor("A{$row}:J{$row}", 'fcf7b6');
                }
            }
            $this->setCellValue('A'.$row, $user->name . $user->pobox_number);
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
            $checkUser = optional($commissionUser)->pobox_number;
        }

        $this->currentRow = $row;
        $endRowSum = $row;
        $endOrderTotal = $row;
        $this->setCellValue('A'.$row, "Number Of Parcels Per Customer : ");
        $this->setCellValue('B'.$row,  "=ROWS($totalOrderRow:D{$endOrderTotal})-2");
        $this->setAlignment('D'.$row,Alignment::HORIZONTAL_LEFT);
        $this->setCellValue('H'.$row, "Due Amount : ");
        $this->setAlignment('H'.$row,Alignment::HORIZONTAL_RIGHT);
        $this->setAlignment('B'.$row,Alignment::HORIZONTAL_LEFT);
        
        $this->setCellValue('I'.$row, "=SUM($sumRow:H{$endRowSum})");
        $this->setAlignment('I'.$row,Alignment::HORIZONTAL_LEFT);
        $this->setBackgroundColor("A{$row}:J{$row}", 'adfb84');
        $row++;
        $this->setCellValue('G'.$row, "Total Due Amount : ");
        $this->setAlignment('G'.$row,Alignment::HORIZONTAL_RIGHT);
        $this->setCellValue('C'.$row, "Total Number of Orders : ");
        $this->setCellValue('D'.$row, $this->sales->count());
        $this->setAlignment('D'.$row,Alignment::HORIZONTAL_LEFT);
        
        $this->setCellValue('H'.$row, "=SUM(H1:H{$row})");
        $this->setAlignment('H'.$row,Alignment::HORIZONTAL_LEFT);
        $this->setBackgroundColor("A{$row}:J{$row}", '3cc4ff');
    }

    private function setExcelHeaderRow()
    {
        if ( Auth::user()->isAdmin() ){
            $this->setColumnWidth('A', 32);
            $this->setCellValue('A1', 'Name');
        }
        
        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'Commission From');

        $this->setColumnWidth('C', 32);
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
