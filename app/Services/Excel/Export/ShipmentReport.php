<?php

namespace App\Services\Excel\Export;

use Illuminate\Support\Collection;
use App\Repositories\Reports\OrderReportsRepository;

class ShipmentReport extends AbstractExportService
{
    private $users;

    private $currentRow = 1;

    public function __construct(Collection $users)
    {
        $this->users = $users;

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
        $orderReportsRepository = new OrderReportsRepository;
        foreach ($this->users as $user) {
            $report = $orderReportsRepository->getShipmentReportOfUsersByWeight($user->id);
            $this->setCellValue('A'.$row, $user->pobox_number);
            $this->setCellValue('B'.$row, $user->name);
            $this->setCellValue('C'.$row, $user->email);
            $this->setCellValue('D'.$row, $user->order_count);
            $this->setCellValue('E'.$row, number_format($user->weight,2));
            $this->setCellValue('F'.$row, number_format($user->spent,2));
            $this->setCellValue('G'.$row, $report[0]['orders']);
            $this->setCellValue('H'.$row, $report[1]['orders']);
            $this->setCellValue('I'.$row, $report[2]['orders']);
            $this->setCellValue('J'.$row, $report[3]['orders']);
            $this->setCellValue('K'.$row, $report[4]['orders']);
            $this->setCellValue('L'.$row, $report[5]['orders']);
            $this->setCellValue('M'.$row, $report[6]['orders']);
            $this->setCellValue('N'.$row, $report[7]['orders']);
            $this->setCellValue('O'.$row, $report[8]['orders']);
            $this->setCellValue('P'.$row, $report[9]['orders']);
            $this->setCellValue('Q'.$row, $report[10]['orders']);
            $this->setCellValue('R'.$row, $report[11]['orders']);
            $this->setCellValue('S'.$row, $report[12]['orders']);
            $this->setCellValue('T'.$row, $report[13]['orders']);
            $row++;
        }

        $this->currentRow = $row;
    }

    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'POBOX#');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'Name');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'Email');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', '# of Shipments');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'Weight in Kg');

        $this->setColumnWidth('F', 20);
        $this->setCellValue('F1', 'Spent USD');

        $this->setColumnWidth('G', 20);
        $this->setCellValue('G1', '0.00 - 1.00 Kg');

        $this->setColumnWidth('H', 20);
        $this->setCellValue('H1', '1.01 - 2.00 Kg');

        $this->setColumnWidth('I', 20);
        $this->setCellValue('I1', '2.01 - 3.00 Kg');

        $this->setColumnWidth('J', 20);
        $this->setCellValue('J1', '3.01 - 4.00 Kg');

        $this->setColumnWidth('K', 20);
        $this->setCellValue('K1', '4.01 - 5.00 Kg');

        $this->setColumnWidth('L', 20);
        $this->setCellValue('L1', '5.01 - 6.00 Kg');

        $this->setColumnWidth('M', 20);
        $this->setCellValue('M1', '6.01 - 7.00 Kg');

        $this->setColumnWidth('N', 20);
        $this->setCellValue('N1', '7.01 - 8.00 Kg');

        $this->setColumnWidth('O', 20);
        $this->setCellValue('O1', '8.01 - 9.00 Kg');

        $this->setColumnWidth('P', 20);
        $this->setCellValue('P1', '9.01 - 10.00 Kg');

        $this->setColumnWidth('Q', 20);
        $this->setCellValue('Q1', '10.01 - 15.00 Kg');

        $this->setColumnWidth('R', 20);
        $this->setCellValue('R1', '15.01 - 20.00 Kg');

        $this->setColumnWidth('S', 20);
        $this->setCellValue('S1', '20.01 - 25.00 Kg');

        $this->setColumnWidth('T', 20);
        $this->setCellValue('T1', '25.01 - 30.00 Kg');

        $this->setBackgroundColor('A1:T1', '2b5cab');
        $this->setColor('A1:T1', 'FFFFFF');

        $this->currentRow++;
    }
}
