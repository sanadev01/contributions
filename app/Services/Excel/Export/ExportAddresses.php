<?php

namespace App\Services\Excel\Export;

use Illuminate\Support\Collection;

class ExportAddresses extends AbstractExportService
{
    private $addresses;

    private $currentRow = 1;

    public function __construct(Collection $addresses)
    {
        $this->addresses = $addresses;

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

        foreach ($this->addresses as $address) {
            $this->setCellValue('A'.$row, $address->user->name .' '. $address->user->last_name);
            $this->setCellValue('B'.$row, $address->first_name.' '.$address->last_name);
            $this->setCellValue('C'.$row, $address->address);
            $this->setCellValue('D'.$row, $address->address2);
            $this->setCellValue('E'.$row, $address->street_no);
            $this->setCellValue('F'.$row, $address->country->name);
            $this->setCellValue('G'.$row, $address->city);
            $this->setCellValue('H'.$row, $address->state->code ?? '');
            if ( $address->account_type == 'individual' ) {
                $this->setCellValue('I'.$row, $address->tax_id);
            }
            if ( $address->account_type == 'business' ) {
                $this->setCellValue('J'.$row, $address->tax_id);
            }
            $this->setCellValue('K'.$row, $address->phone);
            $row++;
        }

        $this->currentRow = $row;
    }

    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'User');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'Name');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'Address');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'Address 2');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'House Number');

        $this->setColumnWidth('F', 25);
        $this->setCellValue('F1', 'Country');

        $this->setColumnWidth('G', 25);
        $this->setCellValue('G1', 'City');

        $this->setColumnWidth('H', 25);
        $this->setCellValue('H1', 'State');

        $this->setColumnWidth('I', 25);
        $this->setCellValue('I1', 'CPF');

        $this->setColumnWidth('J', 25);
        $this->setCellValue('J1', 'CNPJ');

        $this->setColumnWidth('K', 25);
        $this->setCellValue('K1', 'Phone');

        $this->setBackgroundColor('A1:K1', '2b5cab');
        $this->setColor('A1:K1', 'FFFFFF');

        $this->currentRow++;
    }

}
