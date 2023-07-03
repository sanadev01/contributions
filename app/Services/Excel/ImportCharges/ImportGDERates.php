<?php

namespace App\Services\Excel\ImportCharges;

use App\Models\Rate;
use App\Models\Region;
use Illuminate\Http\UploadedFile;
use App\Services\Excel\AbstractImportService;
use Illuminate\Support\Facades\Log;

class ImportGDERates extends AbstractImportService
{
    protected $shippingService;
    protected $countryId;
    protected $regionId;
    private $regions;
    private $regionCodes = [];

    public function __construct(UploadedFile $file, $shippingService, $request)
    {
        $this->shippingService = $shippingService;
        $this->regionId = $request->region_id;
        $this->countryId = $request->country_id;

        $filename = $this->importFile($file);

        parent::__construct(
            $this->getStoragePath($filename)
        );
    }

    public function handle()
    {
        $this->regions = Region::where('country_id', $this->countryId)->orderBy('name')->get();
        $this->getRegionsCodeFromFile();
        dd($this->regions);
        
        return $this->readRatesFromFile();
    }

    private function getRegionsCodeFromFile()
    {
        $this->regionCodes['C'] = $this->workSheet->getCell('C1')->getValue();
        $this->regionCodes['D'] = $this->workSheet->getCell('D1')->getValue();
        $this->regionCodes['E'] = $this->workSheet->getCell('E1')->getValue();
        $this->regionCodes['F'] = $this->workSheet->getCell('F1')->getValue();
        $this->regionCodes['G'] = $this->workSheet->getCell('G1')->getValue();
        $this->regionCodes['H'] = $this->workSheet->getCell('H1')->getValue();
        $this->regionCodes['I'] = $this->workSheet->getCell('I1')->getValue();
        $this->regionCodes['J'] = $this->workSheet->getCell('J1')->getValue();
        $this->regionCodes['K'] = $this->workSheet->getCell('K1')->getValue();
        $this->regionCodes['L'] = $this->workSheet->getCell('L1')->getValue();
        $this->regionCodes['M'] = $this->workSheet->getCell('M1')->getValue();
        $this->regionCodes['N'] = $this->workSheet->getCell('N1')->getValue();
        $this->regionCodes['O'] = $this->workSheet->getCell('O1')->getValue();
        $this->regionCodes['P'] = $this->workSheet->getCell('P1')->getValue();
        $this->regionCodes['Q'] = $this->workSheet->getCell('Q1')->getValue();
        $this->regionCodes['R'] = $this->workSheet->getCell('R1')->getValue();
        $this->regionCodes['S'] = $this->workSheet->getCell('S1')->getValue();
        $this->regionCodes['T'] = $this->workSheet->getCell('T1')->getValue();
        $this->regionCodes['U'] = $this->workSheet->getCell('U1')->getValue();
        $this->regionCodes['V'] = $this->workSheet->getCell('V1')->getValue();
        $this->regionCodes['W'] = $this->workSheet->getCell('W1')->getValue();
        $this->regionCodes['X'] = $this->workSheet->getCell('X1')->getValue();
        $this->regionCodes['Y'] = $this->workSheet->getCell('Y1')->getValue();
        $this->regionCodes['Z'] = $this->workSheet->getCell('Z1')->getValue();
        $this->regionCodes['AA'] = $this->workSheet->getCell('AA1')->getValue();
        $this->regionCodes['AB'] = $this->workSheet->getCell('AB1')->getValue();
        $this->regionCodes['AC'] = $this->workSheet->getCell('AC1')->getValue();
        $this->regionCodes['AD'] = $this->workSheet->getCell('AD1')->getValue();
        $this->regionCodes['AE'] = $this->workSheet->getCell('AE1')->getValue();
        $this->regionCodes['AF'] = $this->workSheet->getCell('AF1')->getValue();
        $this->regionCodes['AG'] = $this->workSheet->getCell('AG1')->getValue();
        $this->regionCodes['AH'] = $this->workSheet->getCell('AH1')->getValue();
        $this->regionCodes['AI'] = $this->workSheet->getCell('AI1')->getValue();
        $this->regionCodes['AJ'] = $this->workSheet->getCell('AJ1')->getValue();
        $this->regionCodes['AK'] = $this->workSheet->getCell('AK1')->getValue();
        $this->regionCodes['AL'] = $this->workSheet->getCell('AL1')->getValue();
        $this->regionCodes['AM'] = $this->workSheet->getCell('AM1')->getValue();
        $this->regionCodes['AN'] = $this->workSheet->getCell('AN1')->getValue();
        $this->regionCodes['AO'] = $this->workSheet->getCell('AO1')->getValue();
        $this->regionCodes['AP'] = $this->workSheet->getCell('AP1')->getValue();
        $this->regionCodes['AQ'] = $this->workSheet->getCell('AQ1')->getValue();
        $this->regionCodes['AR'] = $this->workSheet->getCell('AR1')->getValue();
        $this->regionCodes['AS'] = $this->workSheet->getCell('AS1')->getValue();
        $this->regionCodes['AT'] = $this->workSheet->getCell('AT1')->getValue();
        $this->regionCodes['AU'] = $this->workSheet->getCell('AU1')->getValue();
        $this->regionCodes['AV'] = $this->workSheet->getCell('AV1')->getValue();
        $this->regionCodes['AW'] = $this->workSheet->getCell('AW1')->getValue();
        $this->regionCodes['AX'] = $this->workSheet->getCell('AX1')->getValue();
    }

    public function readRatesFromFile()
    {
        $limit = 69;

        foreach ($this->regionCodes as $cell => $regionCode) {
           $region = $this->regions->firstWhere('code', $regionCode);
           
           $rates = [];
            if ($region) {
                foreach (range(2, $limit) as $row)
                {
                    $rates[] = [
                        'weight' => $this->workSheet->getCell('A'.$row)->getValue(),
                        'leve' => round($this->workSheet->getCell($cell.$row)->getValue(),2)
                    ];
                }
                $this->storeRegionRates($rates, $region->id);
            }
        }

        return true;
    }

    private function storeRegionRates(array $data, $region_id)
    {
        $rates = Rate::where([
            ['shipping_service_id',$this->shippingService->id],
            ['country_id',$this->countryId],
            ['region_id', $region_id]
        ])->first();

        if ( !$rates ){
            $rates= new Rate();
        }

        $rates->shipping_service_id = $this->shippingService->id;
        $rates->country_id = $this->countryId;
        $rates->region_id = $region_id;
        $rates->data = $data;
        $rates->save();
        return true;
    }
}