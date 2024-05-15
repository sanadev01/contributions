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
    public $userId;

    public function __construct(UploadedFile $file, $serviceId, $type, $userId = null)
    {
        $this->serviceId = $serviceId;
        $this->type = $type;
        $this->weightInKG = [];
        $this->weightInLB = [];
        $this->userId = $userId;

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
            'user_id' => $this->userId ?? null,
            'rates' => json_encode($zoneData),
        ];

        return $this->storeRatesToDB($rates);
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

    private function storeRatesToDB(array $data)
    {
        foreach ($data as $rate) {
            $service = ['shipping_service_id' => $rate['shipping_service_id']];

            // Check if user_id is provided and exists
            if ($this->userId !== null && $existingRate = ZoneRate::where(array_merge($service, ['user_id' => $this->userId]))->first()) {
                $existingRate->update([
                    $this->type === 'Cost' ? 'cost_rates' : 'selling_rates' => $rate['rates']
                ]);
            } else {
                // Create a new row with user_id or update existing row without user_id
                $attributes = $this->userId !== null ? array_merge($service, ['user_id' => $this->userId]) : $service;
                ZoneRate::updateOrCreate(
                    $attributes,
                    [
                        $this->type === 'Cost' ? 'cost_rates' : 'selling_rates' => $rate['rates']
                    ]
                );
            }
        }
    }

}
