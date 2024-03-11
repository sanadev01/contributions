<?php

namespace App\Services\Excel\ImportCharges;

use App\Models\ZoneRate;
use Illuminate\Http\UploadedFile;
use App\Services\Excel\AbstractImportService;

class ImportZoneRate extends AbstractImportService
{
    public $serviceId;
    public $type;
    public $weightInKG;
    public $weightInLB;

    public function __construct(UploadedFile $file, $serviceId, $type)
    {
        $this->serviceId = $serviceId;
        $this->type = $type;
        $this->weightInKG = [];
        $this->weightInLB = [];

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

        $zones = [];
        $zoneData = [];

        foreach (range(2, $limit) as $row) {

            $weightInKG = $this->getValueOrDefault('A' . $row);
            $weightInLB = $this->getValueOrDefault('B' . $row);

            if (!empty($weightInKG) && !empty($weightInLB)) {
                $this->weightInKG[] = $weightInKG;
                $this->weightInLB[] = $weightInLB;
            }

        }

        foreach (range('C', 'Z') as $column) {
            $zone = $this->getValueOrDefault($column . '1');
            if (!empty($zone)) {
                $zoneData[$zone] = $this->getZoneData($column);

                $zones[] = $zone;
            } else {
                break; 
            }
        }

        $rates[] = [
            'shipping_service_id' => $this->serviceId,
            'rates' => json_encode($zoneData)
            ];

        return $this->storeRatesToDb($rates);
    }

    private function getZoneData($col) {

        $data =[];
        foreach ($this->weightInKG as $key=> $row) {

            $data['data'][$row.""] = $this->workSheet->getCell($col.($key+4))->getValue();
        }
        return $data;
    }

    private function getValueOrDefault($cell, $default = '')
    {
        $cellValue = $this->workSheet->getCell($cell)->getValue();

        return $cellValue !== null ? $cellValue : $default;
    }

    private function storeRatesToDb(array $data)
    {
        foreach ($data as $rate) {
            $service = ['shipping_service_id' => $rate['shipping_service_id']];

            if ($this->type === 'Cost') {
                ZoneRate::updateOrCreate(
                    $service,
                    ['cost_rates' => $rate['rates']]
                );
            } elseif ($this->type === 'Selling') {
                ZoneRate::updateOrCreate(
                    $service,
                    ['selling_rates' => $rate['rates']]
                );
            }
        }
    }
}
