<?php

namespace App\Services\Excel\ImportCharges;
use App\Models\Country;
use Illuminate\Http\UploadedFile;
use App\Models\ZoneCountry;
use App\Services\Excel\AbstractImportService;

class ImportZoneProfit extends AbstractImportService
{

    public function __construct(UploadedFile $file, $serviceId)
    {
        $this->serviceId = $serviceId;

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
        $currentGroupId = null;
        $countriesNotFound = [];

        foreach (range(3, $limit) as $row) {
            $groupId = $this->getValueOrDefault('A' . $row);
            $countryCode = str_replace(',', '', $this->getValueOrDefault('B' . $row));

            if (!empty($groupId)) {
                $currentGroupId = $groupId;
            }

            if (empty($countryCode)||!is_numeric($currentGroupId)) {
                continue;
            }

            $countryId = Country::where('name', 'like', '%' . $countryCode . '%')->value('id');

            if ($countryId) {
                $rates[] = [
                    'group_id' => $currentGroupId,
                    'country_id' => $countryId,
                    'shipping_service_id' => $this->serviceId,
                    'profit_percentage' => round($this->getValueOrDefault('C' . $row), 2),
                ];
            } else {
                $countriesNotFound[] = $countryCode;
            }
        }

        // dd($countriesNotFound);

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
                    'group_id' => $rate['group_id'],
                    'shipping_service_id' => $rate['shipping_service_id'],
                    'country_id' => $rate['country_id'],
                ],
                [
                    'profit_percentage' => $rate['profit_percentage'],
                ]
            );
        }
    }

}
