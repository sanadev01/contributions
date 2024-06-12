<?php

namespace App\Services\Excel\ImportCharges;

use App\Models\Setting;
use App\Models\PackagePlusRate;
use Illuminate\Http\UploadedFile;
use App\Services\Excel\AbstractImportService;

class ImportPackagePlus extends AbstractImportService
{
    const FILE_TYPE_FREIGHT = 'freight';
    const FILE_TYPE_CAPITAL = 'capital';
    const FILE_TYPE_INTERIOR = 'interior';

    private $filetype;

    public function __construct(UploadedFile $file, $fileType)
    {
        $this->filetype = $fileType;

        $this->setService(self::SERVICE_PACKAGE_PLUS);

        $filename = $this->importFile($file);

        parent::__construct(
            $this->getStoragePath($filename)
        );
    }

    public function handle()
    {
        if ($this->filetype == self::FILE_TYPE_FREIGHT) {
            $data = $this->getFreightData();
            return $this->storeFreightData($data);
        }

        if ($this->filetype == self::FILE_TYPE_CAPITAL) {
            return $this->storeCapitalData(
                $this->getRatesData()
            );
        }

        if ($this->filetype == self::FILE_TYPE_INTERIOR) {
            return  $this->storeInteriorData(
                $this->getRatesData()
            );
        }
    }

    public function getFreightData()
    {
        $row = 2;

        $rates['min'] = $this->workSheet->getCell('A'.$row)->getValue();
        $rates['lt_45'] = $this->workSheet->getCell('B'.$row)->getValue();
        $rates['gt_45'] = $this->workSheet->getCell('C'.$row)->getValue();
        $rates['gt_100'] = $this->workSheet->getCell('D'.$row)->getValue();
        $rates['gt_300'] = $this->workSheet->getCell('E'.$row)->getValue();

        return $rates;
    }

    public function getRatesData()
    {
        $capitalData = [];

        foreach (range(2, $this->noRows) as $row) {
            $capitalData[] = [
                'uf' => $this->workSheet->getCell('A'.$row)->getValue(),
                'additional_per_kg' => $this->workSheet->getCell('CY'.$row)->getValue(),
                'rates' => $this->readRatesFromFile($row)
            ];
        }

        return $capitalData;
    }

    private function readRatesFromFile($row)
    {
        $rates['0.50'] = $this->workSheet->getCell('B'.$row)->getValue();
        $weight = 1;
        for ($cell = 'C'; $cell != $this->noColumns; $cell++) {
            $rates[$weight] = $this->workSheet->getCell($cell.$row)->getValue();
            $weight++;
        }
        return $rates;
    }

    private function storeFreightData($data)
    {
        setting(Setting::PACKAGE_PLUS_FREIGHT, json_encode($data));
        return $packagePlus;
    }

    private function storeCapitalData(array $capitals)
    {
        // Get Data from Excel as Array
        foreach ($capitals as $capital) {
            $inserted = PackagePlusRate::insertOrUpdate([
                'uf' => $capital['uf'],
                'capital' => $capital['rates'],
            ]);

            $inserted->updateExtraArray([
                'capital_extra' => [
                    'additional_kg' => $capital['additional_per_kg']
                ]
            ]);
        }
    }

    private function storeInteriorData(array $interiors)
    {
        // Get Data from Excel as Array
        foreach ($interiors as $interior) {
            $inserted = PackagePlusRate::insertOrUpdate([
                'uf' => $interior['uf'],
                'interior' => $interior['rates'],
            ]);

            $inserted->updateExtraArray([
                'interior_extra' => [
                    'additional_kg' => $interior['additional_per_kg']
                ]
            ]);
        }
    }
}
