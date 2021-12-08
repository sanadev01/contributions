<?php

namespace App\Services\Excel\Export;

use Illuminate\Support\Collection;
use App\Repositories\Reports\OrderReportsRepository;

class ShipmentReportByMonth extends AbstractExportService
{
    private $months;

    private $currentRow = 1;

    public function __construct(Collection $months)
    {
        $this->months = $months;

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
        foreach ($this->months as $month) {
            $report = $orderReportsRepository->getShipmentReportOfUsersByWeight($id = null);
            $this->setCellValue('A'.$row, $this->getMonthName($month->month));
            $this->setCellValue('B'.$row, $month->total);
            $this->setCellValue('C'.$row, round($month->weight,2));
            $this->setCellValue('D'.$row, round($month->spent,2));
            $this->setCellValue('E'.$row, $report[0]['orders']);
            $this->setCellValue('F'.$row, $report[1]['orders']);
            $this->setCellValue('G'.$row, $report[2]['orders']);
            $this->setCellValue('H'.$row, $report[3]['orders']);
            $this->setCellValue('I'.$row, $report[4]['orders']);
            $this->setCellValue('J'.$row, $report[5]['orders']);
            $this->setCellValue('K'.$row, $report[6]['orders']);
            $this->setCellValue('L'.$row, $report[7]['orders']);
            $this->setCellValue('M'.$row, $report[8]['orders']);
            $this->setCellValue('N'.$row, $report[9]['orders']);
            $this->setCellValue('O'.$row, $report[10]['orders']);
            $this->setCellValue('P'.$row, $report[11]['orders']);
            $this->setCellValue('Q'.$row, $report[12]['orders']);
            $this->setCellValue('R'.$row, $report[13]['orders']);
            $row++;
        }
        $this->setCellValue('B'.$row, "=SUM(B1:B{$row})");
        $this->setCellValue('C'.$row, "=SUM(C1:C{$row})");
        $this->setCellValue('D'.$row, "=SUM(D1:D{$row})");
        $this->setCellValue('E'.$row, "=SUM(E1:E{$row})");
        $this->setCellValue('F'.$row, "=SUM(F1:F{$row})");
        $this->setCellValue('G'.$row, "=SUM(G1:G{$row})");
        $this->setCellValue('H'.$row, "=SUM(H1:H{$row})");
        $this->setCellValue('I'.$row, "=SUM(I1:I{$row})");
        $this->setCellValue('J'.$row, "=SUM(J1:K{$row})");
        $this->setCellValue('K'.$row, "=SUM(K1:K{$row})");
        $this->setCellValue('L'.$row, "=SUM(L1:L{$row})");
        $this->setCellValue('M'.$row, "=SUM(M1:M{$row})");
        $this->setCellValue('N'.$row, "=SUM(N1:N{$row})");
        $this->setCellValue('O'.$row, "=SUM(O1:O{$row})");
        $this->setCellValue('p'.$row, "=SUM(P1:Q{$row})");
        $this->setCellValue('Q'.$row, "=SUM(Q1:Q{$row})");
        $this->setCellValue('R'.$row, "=SUM(R1:R{$row})");
        $this->setBackgroundColor("A{$row}:R{$row}", 'adfb84');
        $this->currentRow = $row;
    }

    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'Month');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', '# of Shipments');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'Weight in Kg');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'Spent USD');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', '0.00 - 1.00 Kg');

        $this->setColumnWidth('F', 20);
        $this->setCellValue('F1', '1.01 - 2.00 Kg');

        $this->setColumnWidth('G', 20);
        $this->setCellValue('G1', '2.01 - 3.00 Kg');

        $this->setColumnWidth('H', 20);
        $this->setCellValue('H1', '3.01 - 4.00 Kg');

        $this->setColumnWidth('I', 20);
        $this->setCellValue('I1', '4.01 - 5.00 Kg');

        $this->setColumnWidth('J', 20);
        $this->setCellValue('J1', '5.01 - 6.00 Kg');

        $this->setColumnWidth('K', 20);
        $this->setCellValue('K1', '6.01 - 7.00 Kg');

        $this->setColumnWidth('L', 20);
        $this->setCellValue('L1', '7.01 - 8.00 Kg');

        $this->setColumnWidth('M', 20);
        $this->setCellValue('M1', '8.01 - 9.00 Kg');

        $this->setColumnWidth('N', 20);
        $this->setCellValue('N1', '9.01 - 10.00 Kg');

        $this->setColumnWidth('O', 20);
        $this->setCellValue('O1', '10.01 - 15.00 Kg');

        $this->setColumnWidth('P', 20);
        $this->setCellValue('P1', '15.01 - 20.00 Kg');

        $this->setColumnWidth('Q', 20);
        $this->setCellValue('Q1', '20.01 - 25.00 Kg');

        $this->setColumnWidth('R', 20);
        $this->setCellValue('R1', '25.01 - 30.00 Kg');

        $this->setBackgroundColor('A1:R1', '2b5cab');
        $this->setColor('A1:R1', 'FFFFFF');

        $this->currentRow++;
    }

    private function getMonthName($month)
    {
        $monthNames = array(
            '1' => 'January',
            '2' => 'February',
            '3' => 'March',
            '4' => 'April',
            '5' => 'May',
            '6' => 'June',
            '7' => 'July',
            '8' => 'August',
            '9' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December'
        );

        return $monthNames[$month];
    }
}
