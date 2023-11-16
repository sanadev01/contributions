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
        $limit = 100;

        foreach (range(3, $limit) as $row) {

            // $weight = round($this->getValueOrDefault('A'.$row),2);
            
            // if(($this->country_id == Country::Brazil && $weight <= 30000) || ($this->country_id == Country::Chile && $weight <= 50000))
            // {
                $rates[] = [
                    'zone_id' => $this->zoneId,
                    'country_id' => 'A'.$row,
                    'profit_precentage' => round($this->getValueOrDefault('B'.$row),2),
                ];
            // }
            dd($rates); 
        }

        // return $this->storeRatesToDb($rates);
    }

    private function getValueOrDefault($cell,$default = 0)
    {
        $cellValue = $this->workSheet->getCell($cell)->getValue();

        return $cellValue && strlen($cellValue) >0 ? $cellValue : $default;
    }

    private function storeRatesToDb(array $data)
    {
        // ZoneCountry::where('zond_id',$this->zondId)->delete();
        return ZoneCountry::insert($data);
    }
}
