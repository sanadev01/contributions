<?php

namespace App\Services\Excel\ImportCharges;

use App\Models\Rate;
use App\Models\Country;
use Illuminate\Http\UploadedFile;
use App\Models\Warehouse\AccrualRate;
use App\Services\Excel\AbstractImportService;
use App\Models\ShippingService;

class ImportAccrualRates extends AbstractImportService
{
    protected $service;
    protected $country_id;
    protected $anjunService = false;

    public function __construct(UploadedFile $file, $service, $country_id)
    {
        $this->service = $service;
        $this->country_id = $country_id;
        $this->anjunService = ($service == ShippingService::AJ_Packet_Standard || $service == ShippingService::AJ_Packet_Express) ? true : false;

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
        if($this->country_id == Country::Chile)
        {
            $limit = 75;
        }else{
            $limit = 70;
        }

        foreach (range(3, $limit) as $row) {

            $weight = round($this->getValueOrDefault('A'.$row),2);
            
            if(($this->country_id == Country::Brazil && $weight <= 30000) || ($this->country_id == Country::Chile && $weight <= 50000) || ($this->country_id == Country::COLOMBIA && $weight <= 30000))
            {
                $rates[] = [
                    'service' => $this->service,
                    'country_id' => $this->country_id,
                    'weight' => round($this->getValueOrDefault('A'.$row),2),
                    'cwb' => round($this->getValueOrDefault('C'.$row),2),
                    'gru' => round($this->getValueOrDefault('D'.$row),2),
                    'commission' => ($this->anjunService) ? round($this->getValueOrDefault('E'.$row),2) : 0,
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
        AccrualRate::where('service',$this->service)->delete();
        return AccrualRate::insert($data);
    }
}
