<?php

namespace App\Services\Excel\Export;
use Illuminate\Support\Collection;

class TempOrderExport extends AbstractExportService
{
    private $orders;

    private $currentRow = 1;

    public function __construct(Collection $orders)
    {
        $this->orders = $orders;
        parent::__construct();
    }

    public function handle()
    {
        $this->prepareExcelSheet();

        return $this->downloadExcel();
    }

    private function prepareExcelSheet()
    {
        $this->setExcelHeaderRow();

        $row = $this->currentRow;
        foreach ($this->orders as $order) {
            $user = $order->user;
            $this->setCellValue('A'.$row, $order->merchant);
            $this->setCellValue('B'.$row, $order->carrier);
            $this->setCellValue('C'.$row, $user->tracking_id);
            $this->setCellValue('D'.$row, $order->customer_reference);
            $this->setCellValue('E'.$row, $order->weight);
            $this->setCellValue('F'.$row, $order->lenght);
            $this->setCellValue('G'.$row, $order->width);
            $this->setCellValue('H'.$row, $order->height);
            $this->setCellValue('I'.$row, $order->measurement_unit);
            $this->setCellValue('J'.$row, $order->recipient->first_name);
            $this->setCellValue('K'.$row, $order->recipient->last_name);
            $this->setCellValue('L'.$row, $order->recipient->email);
            $this->setCellValue('M'.$row, $order->recipient->phone);
            $this->setCellValue('N'.$row, $order->recipient->address);
            $this->setCellValue('O'.$row, $order->recipient->address2);
            $this->setCellValue('P'.$row, $order->recipient->street_no);
            $this->setCellValue('Q'.$row, $order->recipient->zipcode);
            $this->setCellValue('R'.$row, $order->recipient->city);
            $this->setCellValue('S'.$row, $order->recipient->state->code);
            $this->setCellValue('T'.$row, $order->recipient->country->code);
            $this->setCellValue('U'.$row, $order->recipient->tax_id);
            $this->setCellValue('V'.$row, $order->user_declared_freight);

            foreach($order->items as $item){
                $this->setCellValue('W'.$row, $item->quantity);
                $this->setCellValue('X'.$row, $item->value);
                $this->setCellValue('Y'.$row, $item->description);
                $this->setCellValue('Z'.$row, $item->ncm);
                $this->setCellValue('AA'.$row, $item->contains_battery?'Yes':"No");
                $this->setCellValue('AB'.$row, $item->contains_perfume?'Yes':"No");
                $row++;
            }
            $row++;   
        }
    }

    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'merchant');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'carrier #');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'tracking id');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'customer reference');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'Weight');

        $this->setColumnWidth('F', 20);
        $this->setCellValue('F1', 'length');

        $this->setColumnWidth('G', 23);
        $this->setCellValue('G1', 'width');

        $this->setColumnWidth('H', 25);
        $this->setCellValue('H1', 'Height');

        $this->setColumnWidth('I', 25);
        $this->setCellValue('I1', 'measurment uni');
        
        $this->setColumnWidth('J', 25);
        $this->setCellValue('J1', 'recipient first name');

        $this->setColumnWidth('K', 20);
        $this->setCellValue('K1', 'recipient last name');
        
        $this->setColumnWidth('L', 20);
        $this->setCellValue('L1', 'recipient email');

        $this->setColumnWidth('M', 20);
        $this->setCellValue('M1', 'recipient phone');
        
        $this->setColumnWidth('N', 20);
        $this->setCellValue('N1', 'recipient address');

        $this->setColumnWidth('O', 20);
        $this->sheet->getStyle('O')->getAlignment()->setHorizontal('center');
        $this->setCellValue('O1', 'recipient address 2');

        $this->setColumnWidth('P', 20);
        $this->setCellValue('P1', 'recipient house number');

        $this->setColumnWidth('Q', 20);
        $this->setCellValue('Q1', 'recipient zipcode');

        $this->setColumnWidth('R', 20);
        $this->setCellValue('R1', 'recipient city');

        $this->setColumnWidth('S', 20);
        $this->setCellValue('S1', 'recipient state');
        
        $this->setColumnWidth('T', 20);
        $this->setCellValue('T1', 'recipient country code');
 
            $this->setColumnWidth('U', 20);
            $this->setCellValue('U1', 'recipient tax id');

            $this->setColumnWidth('V', 20);
            $this->setCellValue('V1', 'freight to custom');

            $this->setColumnWidth('W', 20);
            $this->setCellValue('W1', 'product quantity');

            
            $this->setColumnWidth('X',20);
            $this->setCellValue('X1','product value');
            $this->setColumnWidth('Y',20);
            $this->setCellValue('Y1','product name');
            $this->setColumnWidth('Z', 20);
            $this->setCellValue('Z1',"NCM");
            $this->setColumnWidth('AA', 20);
            $this->setCellValue('AA1', 'Battery');
            $this->setColumnWidth('AB', 20);
            $this->setCellValue('AB1', 'Perfume');

       

        $this->setBackgroundColor('A1:AB1', '2b5cab');
        $this->setColor('A1:AB1', 'FFFFFF');

        $this->currentRow++;
    }
}
