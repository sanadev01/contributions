<?php

namespace App\Services\Excel\Export;

use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use App\Repositories\Reports\OrderReportsRepository;

class ShipmentReportByMonth extends AbstractExportService
{
    private $months;
    private $request;

    private $currentRow = 1;

    public function __construct(Collection $months, Request $request)
    {
        $this->months = $months;
        $this->request = $request;

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
            $report = $orderReportsRepository->getShipmentReportOfUsersByWeight(null,$this->getMonthName($month->month), $this->request);
            \Log::info($month->month);
            \Log::info($this->getMonthName($month->month));
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
        
        $this->setCellValue('B'.$row, "=SUM(B2:B{$row})");
        $this->setCellValue('C'.$row, "=SUM(C2:C{$row})");
        $this->setCellValue('D'.$row, "=SUM(D2:D{$row})");
        $this->setCellValue('E'.$row, "=SUM(E2:E{$row})");
        $this->setCellValue('F'.$row, "=SUM(F2:F{$row})");
        $this->setCellValue('G'.$row, "=SUM(G2:G{$row})");
        $this->setCellValue('H'.$row, "=SUM(H2:H{$row})");
        $this->setCellValue('I'.$row, "=SUM(I2:I{$row})");
        $this->setCellValue('J'.$row, "=SUM(J2:J{$row})");
        $this->setCellValue('K'.$row, "=SUM(K2:K{$row})");
        $this->setCellValue('L'.$row, "=SUM(L2:L{$row})");
        $this->setCellValue('M'.$row, "=SUM(M2:M{$row})");
        $this->setCellValue('N'.$row, "=SUM(N2:N{$row})");
        $this->setCellValue('O'.$row, "=SUM(O2:O{$row})");
        $this->setCellValue('p'.$row, "=SUM(P2:P{$row})");
        $this->setCellValue('Q'.$row, "=SUM(Q2:Q{$row})");
        $this->setCellValue('R'.$row, "=SUM(R2:R{$row})");
        $this->setBackgroundColor("A{$row}:R{$row}", 'adfb84');
        $newRow = $row;
        $newRowPlus = $newRow + 1;
        $this->setCellFormat('E'.$newRowPlus, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $this->setCellFormat('F'.$newRowPlus, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $this->setCellFormat('G'.$newRowPlus, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $this->setCellFormat('H'.$newRowPlus, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $this->setCellFormat('I'.$newRowPlus, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $this->setCellFormat('J'.$newRowPlus, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $this->setCellFormat('K'.$newRowPlus, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $this->setCellFormat('L'.$newRowPlus, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $this->setCellFormat('M'.$newRowPlus, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $this->setCellFormat('N'.$newRowPlus, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $this->setCellFormat('O'.$newRowPlus, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $this->setCellFormat('p'.$newRowPlus, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $this->setCellFormat('Q'.$newRowPlus, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $this->setCellFormat('R'.$newRowPlus, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $this->setCellValue('E'.$newRowPlus, "=(E{$row}/B$row)");
        $this->setCellValue('F'.$newRowPlus, "=(F{$row}/B$row)");
        $this->setCellValue('G'.$newRowPlus, "=(G{$row}/B$row)");
        $this->setCellValue('H'.$newRowPlus, "=(H{$row}/B$row)");
        $this->setCellValue('I'.$newRowPlus, "=(I{$row}/B$row)");
        $this->setCellValue('J'.$newRowPlus, "=(J{$row}/B$row)");
        $this->setCellValue('K'.$newRowPlus, "=(K{$row}/B$row)");
        $this->setCellValue('L'.$newRowPlus, "=(L{$row}/B$row)");
        $this->setCellValue('M'.$newRowPlus, "=(M{$row}/B$row)");
        $this->setCellValue('N'.$newRowPlus, "=(N{$row}/B$row)");
        $this->setCellValue('O'.$newRowPlus, "=(O{$row}/B$row)");
        $this->setCellValue('p'.$newRowPlus, "=(P{$row}/B$row)");
        $this->setCellValue('Q'.$newRowPlus, "=(Q{$row}/B$row)");
        $this->setCellValue('R'.$newRowPlus, "=(R{$row}/B$row)");
        $this->setBackgroundColor("A{$newRowPlus}:R{$newRowPlus}", '3490dc');
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
