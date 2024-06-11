<?php

namespace App\Services\Excel\Export;

use App\Models\ProfitPackage;
use App\Models\ProfitSetting;
use Illuminate\Support\Collection;

class ExportUsers extends AbstractExportService
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

        foreach ($this->users as $user) {
            $this->setCellValue('A'.$row, $user->pobox_number);
            $this->setCellValue('B'.$row, $user->name);
            $this->setCellValue('C'.$row, $user->email);
            $this->setCellValue('D'.$row, $user->accountType());
            $this->setCellValue('E'.$row, $user->come_from);
            $this->setCellValue('F'.$row, optional($user->profitPackage)->name);
            $this->setCellValue('G'.$row, $this->getProfitPackageSettings($user->id));
            $this->setCellValue('H'.$row, setting('marketplace', null, $user->id));
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
        $this->setCellValue('D1', 'Account Type');
        
        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'Referral');

        $this->setColumnWidth('F', 25);
        $this->setCellValue('F1', 'Default Package');

        $this->setColumnWidth('G', 25);
        $this->setCellValue('G1', 'User Setting Package');
        
        $this->setColumnWidth('H', 25);
        $this->setCellValue('H1', 'Marketplace');

        $this->setBackgroundColor('A1:H1', '2b5cab');
        $this->setColor('A1:H1', 'FFFFFF');

        $this->currentRow++;
    }


    private function getProfitPackageSettings($userId)
    {
        if(!$userId)
        {
            return '';
        }

        $settings = ProfitSetting::where('user_id', $userId)->get();

        if(!$settings)
        {
            return '';
        }

        $packages = $settings->map(function($setting) {
            $package = ProfitPackage::find($setting->package_id);
            if($package)
            {
                return isset($package['name']) ? $package['name'] : '';
            }
        });
        
        return $packages->implode(', ');
    }
}
