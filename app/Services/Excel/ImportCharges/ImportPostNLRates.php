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
    private $countryCodes = [];

    public function __construct(UploadedFile $file, $shippingService, $request)
    {
        $this->shippingService = $shippingService;

        $filename = $this->importFile($file);

        parent::__construct(
            $this->getStoragePath($filename)
        );
    }

    public function handle()
    {
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
        $this->countryCodes['M'] = $this->workSheet->getCell('M1')->getValue();
        $this->countryCodes['N'] = $this->workSheet->getCell('N1')->getValue();
        $this->countryCodes['O'] = $this->workSheet->getCell('O1')->getValue();
        $this->countryCodes['P'] = $this->workSheet->getCell('P1')->getValue();
        $this->countryCodes['Q'] = $this->workSheet->getCell('Q1')->getValue();
        $this->countryCodes['R'] = $this->workSheet->getCell('R1')->getValue();
        $this->countryCodes['S'] = $this->workSheet->getCell('S1')->getValue();
        $this->countryCodes['T'] = $this->workSheet->getCell('T1')->getValue();
        $this->countryCodes['U'] = $this->workSheet->getCell('U1')->getValue();
        $this->countryCodes['V'] = $this->workSheet->getCell('V1')->getValue();
        $this->countryCodes['W'] = $this->workSheet->getCell('W1')->getValue();
        $this->countryCodes['X'] = $this->workSheet->getCell('X1')->getValue();
        $this->countryCodes['Y'] = $this->workSheet->getCell('Y1')->getValue();
        $this->countryCodes['Z'] = $this->workSheet->getCell('Z1')->getValue();
        $this->countryCodes['AA'] = $this->workSheet->getCell('AA1')->getValue();
        $this->countryCodes['AB'] = $this->workSheet->getCell('AB1')->getValue();
        $this->countryCodes['AC'] = $this->workSheet->getCell('AC1')->getValue();
        $this->countryCodes['AD'] = $this->workSheet->getCell('AD1')->getValue();
        $this->countryCodes['AE'] = $this->workSheet->getCell('AE1')->getValue();
        $this->countryCodes['AF'] = $this->workSheet->getCell('AF1')->getValue();
        $this->countryCodes['AG'] = $this->workSheet->getCell('AG1')->getValue();
        $this->countryCodes['AH'] = $this->workSheet->getCell('AH1')->getValue();
        $this->countryCodes['AI'] = $this->workSheet->getCell('AI1')->getValue();
        $this->countryCodes['AJ'] = $this->workSheet->getCell('AJ1')->getValue();
        $this->countryCodes['AK'] = $this->workSheet->getCell('AK1')->getValue();
        $this->countryCodes['AL'] = $this->workSheet->getCell('AL1')->getValue();
        $this->countryCodes['AM'] = $this->workSheet->getCell('AM1')->getValue();
        $this->countryCodes['AN'] = $this->workSheet->getCell('AN1')->getValue();
        $this->countryCodes['AO'] = $this->workSheet->getCell('AO1')->getValue();
        $this->countryCodes['AP'] = $this->workSheet->getCell('AP1')->getValue();
        $this->countryCodes['AQ'] = $this->workSheet->getCell('AQ1')->getValue();
        $this->countryCodes['AR'] = $this->workSheet->getCell('AR1')->getValue();
        $this->countryCodes['AS'] = $this->workSheet->getCell('AS1')->getValue();
        $this->countryCodes['AT'] = $this->workSheet->getCell('AT1')->getValue();
        $this->countryCodes['AU'] = $this->workSheet->getCell('AU1')->getValue();
        $this->countryCodes['AV'] = $this->workSheet->getCell('AV1')->getValue();
        $this->countryCodes['AW'] = $this->workSheet->getCell('AW1')->getValue();
        $this->countryCodes['AX'] = $this->workSheet->getCell('AX1')->getValue();
        $this->countryCodes['AY'] = $this->workSheet->getCell('AY1')->getValue();
        $this->countryCodes['AZ'] = $this->workSheet->getCell('AZ1')->getValue();
        $this->countryCodes['BA'] = $this->workSheet->getCell('BA1')->getValue();
        $this->countryCodes['BB'] = $this->workSheet->getCell('BB1')->getValue();
        $this->countryCodes['BC'] = $this->workSheet->getCell('BC1')->getValue();
        $this->countryCodes['BD'] = $this->workSheet->getCell('BD1')->getValue();
        $this->countryCodes['BE'] = $this->workSheet->getCell('BE1')->getValue();
        $this->countryCodes['BF'] = $this->workSheet->getCell('BF1')->getValue();
        $this->countryCodes['BG'] = $this->workSheet->getCell('BG1')->getValue();
        $this->countryCodes['BH'] = $this->workSheet->getCell('BH1')->getValue();
        $this->countryCodes['BI'] = $this->workSheet->getCell('BI1')->getValue();
        $this->countryCodes['BJ'] = $this->workSheet->getCell('BJ1')->getValue();
        $this->countryCodes['BK'] = $this->workSheet->getCell('BK1')->getValue();
        $this->countryCodes['BL'] = $this->workSheet->getCell('BL1')->getValue();
        $this->countryCodes['BM'] = $this->workSheet->getCell('BM1')->getValue();
        $this->countryCodes['BN'] = $this->workSheet->getCell('BN1')->getValue();
        $this->countryCodes['BO'] = $this->workSheet->getCell('BO1')->getValue();
        $this->countryCodes['BP'] = $this->workSheet->getCell('BP1')->getValue();
        $this->countryCodes['BQ'] = $this->workSheet->getCell('BQ1')->getValue();
        $this->countryCodes['BR'] = $this->workSheet->getCell('BR1')->getValue();
        $this->countryCodes['BS'] = $this->workSheet->getCell('BS1')->getValue();
        $this->countryCodes['BT'] = $this->workSheet->getCell('BT1')->getValue();
        $this->countryCodes['BU'] = $this->workSheet->getCell('BU1')->getValue();
        $this->countryCodes['BV'] = $this->workSheet->getCell('BV1')->getValue();
        $this->countryCodes['BW'] = $this->workSheet->getCell('BW1')->getValue();
        $this->countryCodes['BX'] = $this->workSheet->getCell('BX1')->getValue();
        $this->countryCodes['BY'] = $this->workSheet->getCell('BY1')->getValue();
        $this->countryCodes['BZ'] = $this->workSheet->getCell('BZ1')->getValue();
        $this->countryCodes['CA'] = $this->workSheet->getCell('CA1')->getValue();
        $this->countryCodes['CB'] = $this->workSheet->getCell('CB1')->getValue();
        $this->countryCodes['CC'] = $this->workSheet->getCell('CC1')->getValue();
        $this->countryCodes['CD'] = $this->workSheet->getCell('CD1')->getValue();
        $this->countryCodes['CE'] = $this->workSheet->getCell('CE1')->getValue();
        $this->countryCodes['CF'] = $this->workSheet->getCell('CF1')->getValue();
        $this->countryCodes['CG'] = $this->workSheet->getCell('CG1')->getValue();
        $this->countryCodes['CH'] = $this->workSheet->getCell('CH1')->getValue();
        $this->countryCodes['CI'] = $this->workSheet->getCell('CI1')->getValue();
        $this->countryCodes['CJ'] = $this->workSheet->getCell('CJ1')->getValue();
        $this->countryCodes['CK'] = $this->workSheet->getCell('CK1')->getValue();
        $this->countryCodes['CL'] = $this->workSheet->getCell('CL1')->getValue();
        $this->countryCodes['CM'] = $this->workSheet->getCell('CM1')->getValue();
        $this->countryCodes['CN'] = $this->workSheet->getCell('CN1')->getValue();
        $this->countryCodes['CO'] = $this->workSheet->getCell('CO1')->getValue();
        $this->countryCodes['CP'] = $this->workSheet->getCell('CP1')->getValue();
        $this->countryCodes['CQ'] = $this->workSheet->getCell('CQ1')->getValue();
        $this->countryCodes['CR'] = $this->workSheet->getCell('CR1')->getValue();
        $this->countryCodes['CS'] = $this->workSheet->getCell('CS1')->getValue();
        $this->countryCodes['CT'] = $this->workSheet->getCell('CT1')->getValue();
        $this->countryCodes['CU'] = $this->workSheet->getCell('CU1')->getValue();
        $this->countryCodes['CV'] = $this->workSheet->getCell('CV1')->getValue();
        $this->countryCodes['CW'] = $this->workSheet->getCell('CW1')->getValue();
        $this->countryCodes['CX'] = $this->workSheet->getCell('CX1')->getValue();
        $this->countryCodes['CY'] = $this->workSheet->getCell('CY1')->getValue();
        $this->countryCodes['CZ'] = $this->workSheet->getCell('CZ1')->getValue();
        
    }

    public function readRatesFromFile()
    {
        $limit = 49;
        foreach ($this->countryCodes as $cell => $countryCodes) {
           $countryId = Country::where('name', $countryCodes)->value('id');
           $rates = [];
            if ($countryId) {
                foreach (range(2, $limit) as $row)
                {
                    $rates[] = [
                        'weight' => $this->workSheet->getCell('A'.$row)->getValue(),
                        'leve' => round($this->workSheet->getCell($cell.$row)->getValue(),2)
                    ];
                }
                $checkrates = Rate::where([
                    ['shipping_service_id',$this->shippingService->id],
                    ['country_id',$countryId],
                ])->first();
        
                if ( !$checkrates ){
                    $newRates= new Rate();
                }
        
                $newRates->shipping_service_id = $this->shippingService->id;
                $newRates->country_id = $countryId;
                $newRates->data = $rates;
                $newRates->save();
            }
        }

        return true;
    }

}