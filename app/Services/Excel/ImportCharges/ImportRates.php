<?php

namespace App\Services\Excel\ImportCharges;

use App\Models\Rate;
use Illuminate\Http\UploadedFile;
use App\Services\Excel\AbstractImportService;

class ImportRates extends AbstractImportService
{
    protected $shippingSrviceId;
    protected $countryId;
    protected $regionId;

    public function __construct(UploadedFile $file, $shippingSrviceId, $countryId, $regionId = null)
    {
        $this->shippingSrviceId = $shippingSrviceId;
        $this->countryId = $countryId;
        $this->regionId = $regionId;

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
        if($this->countryId == 46){
            $limit = 75;
        }else{
            $limit = 70;
        }
        foreach (range(3, $limit) as $row) {
            $rates[] = [
                'weight' => $this->workSheet->getCell('A'.$row)->getValue(),
                'leve' => round($this->workSheet->getCell('C'.$row)->getValue(),2)
            ];
        }

        // $row = 16;

        // $data = [
        //     'rates' => $rates,
        //     'additional_kg' => $this->workSheet->getCell('C'.$row)->getValue(),
        //     'minimum_size' => $this->workSheet->getCell('C'.(++$row))->getValue(),
        //     'max_combine_dim' => $this->workSheet->getCell('C'.(++$row))->getValue(),
        //     'max_single_dim' => $this->workSheet->getCell('C'.(++$row))->getValue(),
        //     'max_weight' => $this->workSheet->getCell('C'.(++$row))->getValue(),
        //     'max_value' => $this->workSheet->getCell('C'.(++$row))->getValue(),
        // ];
        if($this->regionId != null)
        {
            return $this->storeRegionRates($rates);
        }

        return $this->storeRatesToDb($rates);
    }

    private function storeRatesToDb(array $data)
    {
        $rates = Rate::where('shipping_service_id',$this->shippingSrviceId)->first();
        
        if ( !$rates ){
            $rates= new Rate();
        }

        $rates->shipping_service_id = $this->shippingSrviceId;
        $rates->country_id = $this->countryId;
        $rates->data = $data;
        $rates->save();
        return $rates;
    }

    private function storeRegionRates(array $data)
    {
        $rates = Rate::where([
            ['shipping_service_id',$this->shippingSrviceId],
            ['country_id',$this->countryId],
            ['region_id',$this->regionId]
        ])->first();
        
        if ( !$rates ){
            $rates= new Rate();
        }

        $rates->shipping_service_id = $this->shippingSrviceId;
        $rates->country_id = $this->countryId;
        $rates->region_id = $this->regionId;
        $rates->data = $data;
        $rates->save();
        return $rates;
    }
}
