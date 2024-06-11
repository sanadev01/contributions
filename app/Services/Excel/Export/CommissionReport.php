<?php

namespace App\Services\Excel\Export;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Reports\CommissionReportsRepository;

class CommissionReport extends AbstractExportService
{
    private $users;
    private $request;

    private $currentRow = 1;

    public function __construct(Collection $users, Request $request)
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
        $commissionReportsRepository = new CommissionReportsRepository;
        $row = $this->currentRow;
        foreach ($this->users as $user) {

            Auth::user()->isAdmin() ? $userInfo = $user : $userInfo = $user->referrer;

            $this->setCellValue('A'.$row, optional($userInfo)->pobox_number);
            $this->setCellValue('B'.$row, optional($userInfo)->name);
            $this->setCellValue('C'.$row, optional($userInfo)->email);
            $this->setCellValue('D'.$row, optional($user)->sale_count);
            $this->setCellValue('E'.$row, round(optional($user)->commission,2));
            
            if($this->request->yearReport){
                $reportByMonth = $commissionReportsRepository->getCommissionReportOfUserByMonth($userInfo,$this->request);
                
                foreach($reportByMonth as $reportMonth){
                    if($reportMonth->month == 1){
                        $this->setCellValue('F'.$row, $reportMonth->total);
                    }
                    if($reportMonth->month == 2){
                        $this->setCellValue('G'.$row, $reportMonth->total);
                    }
                    if($reportMonth->month == 3){
                        $this->setCellValue('H'.$row, $reportMonth->total);
                    }
                    if($reportMonth->month == 4){
                        $this->setCellValue('I'.$row, $reportMonth->total);
                    }
                    if($reportMonth->month == 5){
                        $this->setCellValue('J'.$row, $reportMonth->total);
                    }
                    if($reportMonth->month == 6){
                        $this->setCellValue('K'.$row, $reportMonth->total);
                    }
                    if($reportMonth->month == 7){
                        $this->setCellValue('L'.$row, $reportMonth->total);
                    }
                    if($reportMonth->month == 8){
                        $this->setCellValue('M'.$row, $reportMonth->total);
                    }
                    if($reportMonth->month == 9){
                        $this->setCellValue('N'.$row, $reportMonth->total);
                    }
                    if($reportMonth->month == 10){
                        $this->setCellValue('O'.$row, $reportMonth->total);
                    }
                    if($reportMonth->month == 11){
                        $this->setCellValue('P'.$row, $reportMonth->total);
                    }
                    if($reportMonth->month == 12){
                        $this->setCellValue('Q'.$row, $reportMonth->total);
                    }
                }
            }
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
        $this->setCellValue('D1', '# of Sales');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'Commission USD');
        if($this->request->yearReport){

            $this->setColumnWidth('F', 20);
            $this->setCellValue('F1', 'January');
            
            $this->setColumnWidth('G', 20);
            $this->setCellValue('G1', 'February');
            
            $this->setColumnWidth('H', 20);
            $this->setCellValue('H1', 'March');
            
            $this->setColumnWidth('I', 20);
            $this->setCellValue('I1', 'April');
            
            $this->setColumnWidth('J', 20);
            $this->setCellValue('J1', 'May');
            
            $this->setColumnWidth('K', 20);
            $this->setCellValue('K1', 'June');
            
            $this->setColumnWidth('L', 20);
            $this->setCellValue('L1', 'July');
            
            $this->setColumnWidth('M', 20);
            $this->setCellValue('M1', 'August');
            
            $this->setColumnWidth('N', 20);
            $this->setCellValue('N1', 'September');
            
            $this->setColumnWidth('O', 20);
            $this->setCellValue('O1', 'October');
            
            $this->setColumnWidth('P', 20);
            $this->setCellValue('P1', 'November');
            
            $this->setColumnWidth('Q', 20);
            $this->setCellValue('Q1', 'December');
        }

        $this->setBackgroundColor('A1:Q1', '2b5cab');
        $this->setColor('A1:Q1', 'FFFFFF');

        $this->currentRow++;
        
    }
}
