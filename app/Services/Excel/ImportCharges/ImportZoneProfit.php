<?php

namespace App\Services\Excel\ImportCharges;

use App\Models\Rate;
use App\Models\Country;
use Illuminate\Http\UploadedFile;
use App\Models\ZoneCountry;
use App\Services\Excel\AbstractImportService;
use App\Models\ShippingService;

class ImportZoneProfit extends AbstractImportService
{

    public function __construct(UploadedFile $file, $zoneId)
    {
        $this->zoneId = $zoneId;

        $filename = $this->importFile($file);

        parent::__construct(
            $this->getStoragePath($filename)
        );
    }

    public function handle()
    {
        return $this->readRatesFromFile();
    }

    public function readRatesFromFile()
    {
        $rates = [];
        $limit = 1000;

        foreach (range(2, $limit) as $row) {
            $countryCode = $this->getValueOrDefault('A'.$row);

            if ($countryCode == null) {
                break;
            }

            $countryId = Country::where('code', $countryCode)->value('id');

            if ($countryId) {
                $rates[] = [
                    'zone_id' => $this->zoneId,
                    'country_id' => $countryId,
                    'profit_percentage' => round($this->getValueOrDefault('B'.$row), 2),
                ];
            }
        }

        return $this->storeRatesToDb($rates);
    }

    private function getValueOrDefault($cell,$default = 0)
    {
        $cellValue = $this->workSheet->getCell($cell)->getValue();

        return $cellValue && strlen($cellValue) >0 ? $cellValue : $default;
    }

    private function storeRatesToDb(array $data)
    {
        foreach ($data as $rate) {
            ZoneCountry::updateOrInsert(
                [
                    'zone_id' => $rate['zone_id'],
                    'country_id' => $rate['country_id'],
                ],
                [
                    'profit_percentage' => $rate['profit_percentage'],
                ]
            );
        }
    }

}
