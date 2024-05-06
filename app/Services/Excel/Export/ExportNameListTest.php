<?php

namespace App\Services\Excel\Export;

use App\Models\User;
use Illuminate\Support\Collection;

class ExportNameListTest extends AbstractExportService
{
    private $orders;
    private $user;

    private $currentRow = 1;

    public function __construct($user_id)
    {
        $this->user   = User::where('pobox_number',$user_id)->orwhere('id',$user_id)->first();
        if(!$this->user){
            abort(404);
        }
        $this->orders = $this->user->orders; 
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

        $row= $this->currentRow;

        foreach ($this->orders as $order) {
            $this->setCellValue('A'.$row, $this->user->pobox_number);
            $this->setCellValue('B'.$row, $order->warehouse_number); 
            $this->setCellValue('C'.$row, $order->getSenderFullName());
            $this->setCellValue('D'.$row, $order->sender_email); 
            $this->setCellValue('E'.$row, optional($order->recipient)->getFullName()); 
            $this->setCellValue('F'.$row, optional($order->recipient)->email);   
            $row++;
        } 
        $this->setBackgroundColor("A{$row}:F{$row}", 'adfb84');
    }

    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'Pobox Number');
        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'Warehouse number');
        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'Sender Name');
        $this->setColumnWidth('D', 30);
        $this->setCellValue('D1', 'Sender Email');
        $this->setColumnWidth('E', 30);
        $this->setCellValue('E1', 'Buyer Name');
        $this->setColumnWidth('F', 30);
        $this->setCellValue('F1', 'Buyer Email'); 
        $this->setBackgroundColor('A1:F1', '2b5cab');
        $this->setColor('A1:F1', 'FFFFFF');


        $this->currentRow++;
    }
}
