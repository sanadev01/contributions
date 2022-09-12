<?php

namespace App\Services\Excel\ImportCharges;

use App\Models\Rate;
use App\Models\Country;
use Illuminate\Http\UploadedFile;
use App\Services\Excel\AbstractImportService;
use Illuminate\Support\Facades\Log;

class ImportPostNLRates extends AbstractImportService
{
    protected $shippingService;
    protected $countryId;
    private $countryCodes = [];

    public function __construct(UploadedFile $file, $shippingService, $request)
    {
        $this->shippingService = $shippingService;
        $this->countryId = $request->country_id;

        $filename = $this->importFile($file);

        parent::__construct(
            $this->getStoragePath($filename)
        );
    }

    public function handle()
    {
        $this->country = Country::where('id', $this->countryId)->orderBy('name')->get();
        $this->getCountryCodeFromFile();
        
        return $this->readRatesFromFile();
    }

    private function getCountryCodeFromFile()
    {
        $this->countryCodes['C'] = $this->workSheet->getCell('C1')->getValue();
        $this->countryCodes['D'] = $this->workSheet->getCell('D1')->getValue();
        $this->countryCodes['E'] = $this->workSheet->getCell('E1')->getValue();
        $this->countryCodes['F'] = $this->workSheet->getCell('F1')->getValue();
        $this->countryCodes['G'] = $this->workSheet->getCell('G1')->getValue();
        $this->countryCodes['H'] = $this->workSheet->getCell('H1')->getValue();
        $this->countryCodes['I'] = $this->workSheet->getCell('I1')->getValue();
        $this->countryCodes['J'] = $this->workSheet->getCell('J1')->getValue();
        $this->countryCodes['K'] = $this->workSheet->getCell('K1')->getValue();
        $this->countryCodes['L'] = $this->workSheet->getCell('L1')->getValue();
        //dd($this->countryCodes['C']);
        
    }

    public function readRatesFromFile()
    {
        $limit = 49;
        foreach ($this->countryCodes as $cell => $countryCodes) {
           $country = $this->country->firstWhere('name',  ucfirst(strtolower($countryCodes)));
           $rates = [];
            if ($country) {
                foreach (range(2, $limit) as $row)
                {
                    $rates[] = [
                        'weight' => $this->workSheet->getCell('A'.$row)->getValue(),
                        'leve' => round($this->workSheet->getCell($cell.$row)->getValue(),2)
                    ];
                }
                $this->storeCountryRates($rates, $country->id);
            }
        }

        return true;
    }

    private function storeCountryRates(array $data, $country)
    {
        //dd($country);
        $rates = Rate::where([
            ['shipping_service_id',$this->shippingService->id],
            ['country_id',$country],
        ])->first();

        if ( !$rates ){
            $rates= new Rate();
        }

        $rates->shipping_service_id = $this->shippingService->id;
        $rates->country_id = $this->countryId;
        $rates->data = $data;
        $rates->save();
        return true;
    }
}