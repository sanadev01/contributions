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
            $this->setCellValue('S'.$row, $report[14]['orders']);
            $this->setCellValue('T'.$row, $report[15]['orders']);
            $this->setCellValue('U'.$row, $report[16]['orders']);
            $this->setCellValue('V'.$row, $report[17]['orders']);
            $this->setCellValue('W'.$row, $report[18]['orders']);
            $this->setCellValue('X'.$row, $report[19]['orders']);
            $this->setCellValue('Y'.$row, $report[20]['orders']);
            $this->setCellValue('Z'.$row, $report[21]['orders']);
            $this->setCellValue('AA'.$row, $report[22]['orders']);
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
        $this->setCellValue('S'.$row, "=SUM(S2:S{$row})");
        $this->setCellValue('T'.$row, "=SUM(T2:T{$row})");
        $this->setCellValue('U'.$row, "=SUM(U2:U{$row})");
        $this->setCellValue('V'.$row, "=SUM(V2:V{$row})");
        $this->setCellValue('W'.$row, "=SUM(W2:W{$row})");
        $this->setCellValue('X'.$row, "=SUM(X2:X{$row})");
        $this->setCellValue('Y'.$row, "=SUM(Y2:Y{$row})");
        $this->setCellValue('Z'.$row, "=SUM(Z2:Z{$row})");
        $this->setCellValue('AA'.$row, "=SUM(AA2:AA{$row})");
        $this->setBackgroundColor("A{$row}:AA{$row}", 'adfb84');
        $newRow = $row;
        $newRowPlus = $newRow + 1;

        $this->setCellFormat('E'.$newRowPlus, '0.00%;[Red]-0.00%');
        $this->setCellFormat('F'.$newRowPlus, '0.00%;[Red]-0.00%');
        $this->setCellFormat('G'.$newRowPlus, '0.00%;[Red]-0.00%');
        $this->setCellFormat('H'.$newRowPlus, '0.00%;[Red]-0.00%');
        $this->setCellFormat('I'.$newRowPlus, '0.00%;[Red]-0.00%');
        $this->setCellFormat('J'.$newRowPlus, '0.00%;[Red]-0.00%');
        $this->setCellFormat('K'.$newRowPlus, '0.00%;[Red]-0.00%');
        $this->setCellFormat('L'.$newRowPlus, '0.00%;[Red]-0.00%');
        $this->setCellFormat('M'.$newRowPlus, '0.00%;[Red]-0.00%');
        $this->setCellFormat('N'.$newRowPlus, '0.00%;[Red]-0.00%');
        $this->setCellFormat('O'.$newRowPlus, '0.00%;[Red]-0.00%');
        $this->setCellFormat('p'.$newRowPlus, '0.00%;[Red]-0.00%');
        $this->setCellFormat('Q'.$newRowPlus, '0.00%;[Red]-0.00%');
        $this->setCellFormat('R'.$newRowPlus, '0.00%;[Red]-0.00%');
        $this->setCellFormat('S'.$newRowPlus, '0.00%;[Red]-0.00%');
        $this->setCellFormat('T'.$newRowPlus, '0.00%;[Red]-0.00%');
        $this->setCellFormat('U'.$newRowPlus, '0.00%;[Red]-0.00%');
        $this->setCellFormat('V'.$newRowPlus, '0.00%;[Red]-0.00%');
        $this->setCellFormat('W'.$newRowPlus, '0.00%;[Red]-0.00%');
        $this->setCellFormat('X'.$newRowPlus, '0.00%;[Red]-0.00%');
        $this->setCellFormat('Y'.$newRowPlus, '0.00%;[Red]-0.00%');
        $this->setCellFormat('Z'.$newRowPlus, '0.00%;[Red]-0.00%');
        $this->setCellFormat('AA'.$newRowPlus, '0.00%;[Red]-0.00%');

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
         $this->setCellValue('S'.$newRowPlus, "=(S{$row}/B$row)");
         $this->setCellValue('T'.$newRowPlus, "=(T{$row}/B$row)");
         $this->setCellValue('U'.$newRowPlus, "=(U{$row}/B$row)");
         $this->setCellValue('V'.$newRowPlus, "=(V{$row}/B$row)");
         $this->setCellValue('W'.$newRowPlus, "=(W{$row}/B$row)");
         $this->setCellValue('X'.$newRowPlus, "=(X{$row}/B$row)");
         $this->setCellValue('Y'.$newRowPlus, "=(Y{$row}/B$row)");
         $this->setCellValue('Z'.$newRowPlus, "=(Z{$row}/B$row)");
         $this->setCellValue('AA'.$newRowPlus, "=(AA{$row}/B$row)"); 
        $this->setBackgroundColor("A{$newRowPlus}:AA{$newRowPlus}", '3490dc');
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
        $this->setCellValue('E1', '0.00 - 0.100 Kg');
        
        $this->setColumnWidth('F', 20);
        $this->setCellValue('F1', '0.101 - 0.200 Kg');

        $this->setColumnWidth('G', 20);
        $this->setCellValue('G1', '0.201 - 0.300 Kg');

        $this->setColumnWidth('H', 20);
        $this->setCellValue('H1', '0.301 - 0.400 Kg');

        $this->setColumnWidth('I', 20);
        $this->setCellValue('I1', '0.401 - 0.500 Kg');

        $this->setColumnWidth('J', 20);
        $this->setCellValue('J1', '0.501 - 0.600 Kg');

        $this->setColumnWidth('K', 20);
        $this->setCellValue('K1', '0.601 - 0.700 Kg');

        $this->setColumnWidth('L', 20);
        $this->setCellValue('L1', '0.701 - 0.800 Kg');

        $this->setColumnWidth('M', 20);
        $this->setCellValue('M1', '0.801 - 0.900 Kg');

        $this->setColumnWidth('N', 20);
        $this->setCellValue('N1', '0.901 - 1.00 Kg');

        $this->setColumnWidth('O', 20);
        $this->setCellValue('O1', '1.01 - 2.00 Kg');

        $this->setColumnWidth('P', 20);
        $this->setCellValue('P1', '2.01 - 3.00 Kg');

        $this->setColumnWidth('Q', 20);
        $this->setCellValue('Q1', '3.01 - 4.00 Kg');

        $this->setColumnWidth('R', 20);
        $this->setCellValue('R1', '4.01 - 5.00 Kg');

        $this->setColumnWidth('S', 20);
        $this->setCellValue('S1', '5.01 - 6.00 Kg');

        $this->setColumnWidth('T', 20);
        $this->setCellValue('T1', '6.01 - 7.00 Kg');

        $this->setColumnWidth('U', 20);
        $this->setCellValue('U1', '7.01 - 8.00 Kg');

        $this->setColumnWidth('V', 20);
        $this->setCellValue('V1', '8.01 - 9.00 Kg');

        $this->setColumnWidth('W', 20);
        $this->setCellValue('W1', '9.01 - 10.00 Kg');

        $this->setColumnWidth('X', 20);
        $this->setCellValue('X1', '10.01 - 15.00 Kg');
        
        $this->setColumnWidth('Y', 20);
        $this->setCellValue('Y1', '15.01 - 20.00 Kg');
        
        $this->setColumnWidth('Z', 20);
        $this->setCellValue('Z1', '20.01 - 25.00 Kg');

        $this->setColumnWidth('AA', 20);
        $this->setCellValue('AA1','25.01 - 30.00 Kg');

        $this->setBackgroundColor('A1:AA1', '2b5cab');
        $this->setColor('A1:AA1', 'FFFFFF');

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
