<?php

namespace App\Services\Excel\ImportCharges;

use App\Models\Rate;
use App\Models\Warehouse\AccrualRate;
use Illuminate\Http\UploadedFile;
use App\Services\Excel\AbstractImportService;

class ImportAccrualRates extends AbstractImportService
{
    protected $service;
    protected $country_id;

    public function __construct(UploadedFile $file, $service, $country_id)
    {
        $this->service = $service;
        $this->country_id = $country_id;

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

        foreach (range(3, 70) as $row) {
            $rates[] = [
                'service' => $this->service,
                'country_id' => $this->country_id,
                'weight' => round($this->getValueOrDefault('A'.$row),2),
                'cwb' => round($this->getValueOrDefault('C'.$row),2),
                'gru' => round($this->getValueOrDefault('D'.$row),2)
            ];
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
        AccrualRate::where('service',$this->service)->delete();
        return AccrualRate::insert($data);
    }
}
