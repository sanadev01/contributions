<?php

namespace App\Services\Excel\Export;

use Illuminate\Support\Collection;
use App\Repositories\Reports\OrderReportsRepository;

class ShipmentReport extends AbstractExportService
{
    private $users;
    private $request;
    
    private $currentRow = 1;

    public function __construct(Collection $users, $request)
    {
        $this->users = $users;
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
        foreach ($this->users as $user) {
            $report = $orderReportsRepository->getShipmentReportOfUsersByWeight($user->id, null, $this->request);
            $reportByService = $orderReportsRepository->orderReportByService($user, $this->request);

            $this->setCellValue('A'.$row, $user->pobox_number);
            $this->setCellValue('B'.$row, $user->name);
            $this->setCellValue('C'.$row, $user->email);
            $this->setCellValue('D'.$row, $user->order_count);
            $this->setCellValue('E'.$row, round($user->weight,2));
            $this->setCellValue('F'.$row, round($user->spent,2));
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
            $this->setCellValue('U'.$row, $report[14]['orders']);
            $this->setCellValue('V'.$row, $report[15]['orders']);
            $this->setCellValue('W'.$row, $report[16]['orders']);
            $this->setCellValue('X'.$row, $report[17]['orders']);
            $this->setCellValue('Y'.$row, $report[18]['orders']);
            $this->setCellValue('Z'.$row, $report[19]['orders']);
            $this->setCellValue('AA'.$row, $report[20]['orders']);
            $this->setCellValue('AB'.$row, $report[21]['orders']);
            $this->setCellValue('AC'.$row, $report[22]['orders']);
            $this->setCellValue('AD'.$row, $reportByService->brazil_order_count);
            $this->setCellValue('AE'.$row, $reportByService->chile_order_count);
            $this->setCellValue('AF'.$row, $reportByService->ups_order_count);
            $this->setCellValue('AG'.$row, $reportByService->usps_order_count);
            $this->setCellValue('AH'.$row, $reportByService->fedex_order_count);
            $this->setCellValue('AI'.$row, $reportByService->gps_order_count);
            $this->setCellValue('AJ'.$row, $reportByService->other_order_count);
            $this->setBackgroundColor("AD{$row}:AJ{$row}", 'd1d1d1');
            $row++;
        }
        $this->setCellValue('D'.$row, "=SUM(D1:D{$row})");
        $this->setCellValue('E'.$row, "=SUM(E1:E{$row})");
        $this->setCellValue('F'.$row, "=SUM(F1:F{$row})");
        $this->setCellValue('G'.$row, "=SUM(G1:G{$row})");
        $this->setCellValue('H'.$row, "=SUM(H1:H{$row})");
        $this->setCellValue('I'.$row, "=SUM(I1:I{$row})");
        $this->setCellValue('J'.$row, "=SUM(J1:J{$row})");
        $this->setCellValue('K'.$row, "=SUM(K1:K{$row})");
        $this->setCellValue('L'.$row, "=SUM(L1:L{$row})");
        $this->setCellValue('M'.$row, "=SUM(M1:M{$row})");
        $this->setCellValue('N'.$row, "=SUM(N1:N{$row})");
        $this->setCellValue('O'.$row, "=SUM(O1:O{$row})");
        $this->setCellValue('P'.$row, "=SUM(P1:P{$row})");
        $this->setCellValue('Q'.$row, "=SUM(Q1:Q{$row})");
        $this->setCellValue('R'.$row, "=SUM(R1:R{$row})");
        $this->setCellValue('S'.$row, "=SUM(S1:S{$row})");
        $this->setCellValue('T'.$row, "=SUM(T1:T{$row})");
        $this->setCellValue('U'.$row, "=SUM(U1:U{$row})");
        $this->setCellValue('V'.$row, "=SUM(V1:V{$row})");
        $this->setCellValue('W'.$row, "=SUM(W1:W{$row})");
        $this->setCellValue('X'.$row, "=SUM(X1:X{$row})");
        $this->setCellValue('Y'.$row, "=SUM(Y1:Y{$row})");
        $this->setCellValue('Z'.$row, "=SUM(Z1:Z{$row})");
        $this->setCellValue('AA'.$row, "=SUM(AA1:AA{$row})");
        $this->setCellValue('AB'.$row, "=SUM(AB1:AB{$row})");
        $this->setCellValue('AC'.$row, "=SUM(AC1:AC{$row})");
        $this->setCellValue('AD'.$row, "=SUM(AD1:AD{$row})");
        $this->setCellValue('AE'.$row, "=SUM(AE1:AE{$row})");
        $this->setCellValue('AF'.$row, "=SUM(AF1:AF{$row})");
        $this->setCellValue('AG'.$row, "=SUM(AG1:AG{$row})");
        $this->setCellValue('AH'.$row, "=SUM(AH1:AH{$row})");
        $this->setCellValue('AI'.$row, "=SUM(AI1:AI{$row})");
        $this->setCellValue('AJ'.$row, "=SUM(AJ1:AJ{$row})");
        $this->setBackgroundColor("A{$row}:AJ{$row}", 'adfb84');
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
        $this->setCellValue('G1', '0.00 - 0.100 Kg');
        
        $this->setColumnWidth('H', 20);
        $this->setCellValue('H1', '0.101 - 0.200 Kg');

        $this->setColumnWidth('I', 20);
        $this->setCellValue('I1', '0.201 - 0.300 Kg');

        $this->setColumnWidth('J', 20);
        $this->setCellValue('J1', '0.301 - 0.400 Kg');

        $this->setColumnWidth('K', 20);
        $this->setCellValue('K1', '0.401 - 0.500 Kg');

        $this->setColumnWidth('L', 20);
        $this->setCellValue('L1', '0.501 - 0.600 Kg');

        $this->setColumnWidth('M', 20);
        $this->setCellValue('M1', '0.601 - 0.700 Kg');

        $this->setColumnWidth('N', 20);
        $this->setCellValue('N1', '0.701 - 0.800 Kg');

        $this->setColumnWidth('O', 20);
        $this->setCellValue('O1', '0.801 - 0.900 Kg');

        $this->setColumnWidth('P', 20);
        $this->setCellValue('P1', '0.901 - 1.00 Kg');

        $this->setColumnWidth('Q', 20);
        $this->setCellValue('Q1', '1.01 - 2.00 Kg');

        $this->setColumnWidth('R', 20);
        $this->setCellValue('R1', '2.01 - 3.00 Kg');

        $this->setColumnWidth('S', 20);
        $this->setCellValue('S1', '3.01 - 4.00 Kg');

        $this->setColumnWidth('T', 20);
        $this->setCellValue('T1', '4.01 - 5.00 Kg');

        $this->setColumnWidth('U', 20);
        $this->setCellValue('U1', '5.01 - 6.00 Kg');

        $this->setColumnWidth('V', 20);
        $this->setCellValue('V1', '6.01 - 7.00 Kg');

        $this->setColumnWidth('W', 20);
        $this->setCellValue('W1', '7.01 - 8.00 Kg');

        $this->setColumnWidth('X', 20);
        $this->setCellValue('X1', '8.01 - 9.00 Kg');

        $this->setColumnWidth('Y', 20);
        $this->setCellValue('Y1', '9.01 - 10.00 Kg');

        $this->setColumnWidth('Z', 20);
        $this->setCellValue('Z1', '10.01 - 15.00 Kg');

        $this->setColumnWidth('AA', 20);
        $this->setCellValue('AA1','15.01 - 20.00 Kg');

        $this->setColumnWidth('AB', 20);
        $this->setCellValue('AB1', '20.01 - 25.00 Kg');

        $this->setColumnWidth('AC', 20);
        $this->setCellValue('AC1', '25.01 - 30.00 Kg');

        $this->setColumnWidth('AD', 20);
        $this->setCellValue('AD1', 'Correios Brazil');
        
        $this->setColumnWidth('AE', 20);
        $this->setCellValue('AE1', 'Correios Chile');
        
        $this->setColumnWidth('AF', 20);
        $this->setCellValue('AF1', 'UPS');
        
        $this->setColumnWidth('AG', 20);
        $this->setCellValue('AG1', 'Usps');
        
        $this->setColumnWidth('AH', 20);
        $this->setCellValue('AH1', 'Fedex');
        
        $this->setColumnWidth('AI', 20);
        $this->setCellValue('AI1', 'GePS');
        
        $this->setColumnWidth('AJ', 20);
        $this->setCellValue('AJ1', 'Old Services');
        
        $this->setBackgroundColor('A1:AJ1', '2b5cab');
        $this->setColor('A1:AJ1', 'FFFFFF');

        $this->currentRow++;
    }
}
