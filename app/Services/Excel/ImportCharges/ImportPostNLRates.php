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
        $this->countryCodes['DA'] = $this->workSheet->getCell('DA1')->getValue();
        $this->countryCodes['DB'] = $this->workSheet->getCell('DB1')->getValue();
        $this->countryCodes['DC'] = $this->workSheet->getCell('DC1')->getValue();
        $this->countryCodes['DD'] = $this->workSheet->getCell('DD1')->getValue();
        $this->countryCodes['DE'] = $this->workSheet->getCell('DE1')->getValue();
        $this->countryCodes['DF'] = $this->workSheet->getCell('DF1')->getValue();
        $this->countryCodes['DG'] = $this->workSheet->getCell('DG1')->getValue();
        $this->countryCodes['DH'] = $this->workSheet->getCell('DH1')->getValue();
        $this->countryCodes['DI'] = $this->workSheet->getCell('DI1')->getValue();
        $this->countryCodes['DJ'] = $this->workSheet->getCell('DJ1')->getValue();
        $this->countryCodes['DK'] = $this->workSheet->getCell('DK1')->getValue();
        $this->countryCodes['DL'] = $this->workSheet->getCell('DL1')->getValue();
        $this->countryCodes['DM'] = $this->workSheet->getCell('DM1')->getValue();
        $this->countryCodes['DN'] = $this->workSheet->getCell('DN1')->getValue();
        $this->countryCodes['DO'] = $this->workSheet->getCell('DO1')->getValue();
        $this->countryCodes['DP'] = $this->workSheet->getCell('DP1')->getValue();
        $this->countryCodes['DQ'] = $this->workSheet->getCell('DQ1')->getValue();
        $this->countryCodes['DR'] = $this->workSheet->getCell('DR1')->getValue();
        $this->countryCodes['DS'] = $this->workSheet->getCell('DS1')->getValue();
        $this->countryCodes['DT'] = $this->workSheet->getCell('DT1')->getValue();
        $this->countryCodes['DU'] = $this->workSheet->getCell('DU1')->getValue();
        $this->countryCodes['DV'] = $this->workSheet->getCell('DV1')->getValue();
        $this->countryCodes['DW'] = $this->workSheet->getCell('DW1')->getValue();
        $this->countryCodes['DX'] = $this->workSheet->getCell('DX1')->getValue();
        $this->countryCodes['DY'] = $this->workSheet->getCell('DY1')->getValue();
        $this->countryCodes['DZ'] = $this->workSheet->getCell('DZ1')->getValue();
        $this->countryCodes['EA'] = $this->workSheet->getCell('EA1')->getValue();
        $this->countryCodes['EB'] = $this->workSheet->getCell('EB1')->getValue();
        $this->countryCodes['EC'] = $this->workSheet->getCell('EC1')->getValue();
        $this->countryCodes['ED'] = $this->workSheet->getCell('ED1')->getValue();
        $this->countryCodes['EE'] = $this->workSheet->getCell('EE1')->getValue();
        $this->countryCodes['EF'] = $this->workSheet->getCell('EF1')->getValue();
        $this->countryCodes['EG'] = $this->workSheet->getCell('EG1')->getValue();
        $this->countryCodes['EH'] = $this->workSheet->getCell('EH1')->getValue();
        $this->countryCodes['EI'] = $this->workSheet->getCell('EI1')->getValue();
        $this->countryCodes['EJ'] = $this->workSheet->getCell('EJ1')->getValue();
        $this->countryCodes['EK'] = $this->workSheet->getCell('EK1')->getValue();
        $this->countryCodes['EL'] = $this->workSheet->getCell('EL1')->getValue();
        $this->countryCodes['EM'] = $this->workSheet->getCell('EM1')->getValue();
        $this->countryCodes['EN'] = $this->workSheet->getCell('EN1')->getValue();
        $this->countryCodes['EO'] = $this->workSheet->getCell('EO1')->getValue();
        $this->countryCodes['EP'] = $this->workSheet->getCell('EP1')->getValue();
        $this->countryCodes['EQ'] = $this->workSheet->getCell('EQ1')->getValue();
        $this->countryCodes['ER'] = $this->workSheet->getCell('ER1')->getValue();
        $this->countryCodes['ES'] = $this->workSheet->getCell('ES1')->getValue();
        $this->countryCodes['ET'] = $this->workSheet->getCell('ET1')->getValue();
        $this->countryCodes['EU'] = $this->workSheet->getCell('EU1')->getValue();
        $this->countryCodes['EV'] = $this->workSheet->getCell('EV1')->getValue();
        $this->countryCodes['EW'] = $this->workSheet->getCell('EW1')->getValue();
        $this->countryCodes['EX'] = $this->workSheet->getCell('EX1')->getValue();
        $this->countryCodes['EY'] = $this->workSheet->getCell('EY1')->getValue();
        $this->countryCodes['EZ'] = $this->workSheet->getCell('EZ1')->getValue();
        $this->countryCodes['FA'] = $this->workSheet->getCell('FA1')->getValue();
        $this->countryCodes['FB'] = $this->workSheet->getCell('FB1')->getValue();
        $this->countryCodes['FC'] = $this->workSheet->getCell('FC1')->getValue();
        $this->countryCodes['FD'] = $this->workSheet->getCell('FD1')->getValue();
        $this->countryCodes['FE'] = $this->workSheet->getCell('FE1')->getValue();
        $this->countryCodes['FF'] = $this->workSheet->getCell('FF1')->getValue();
        $this->countryCodes['FG'] = $this->workSheet->getCell('FG1')->getValue();
        $this->countryCodes['FH'] = $this->workSheet->getCell('FH1')->getValue();
        $this->countryCodes['FI'] = $this->workSheet->getCell('FI1')->getValue();
        $this->countryCodes['FJ'] = $this->workSheet->getCell('FJ1')->getValue();
        $this->countryCodes['FK'] = $this->workSheet->getCell('FK1')->getValue();
        $this->countryCodes['FL'] = $this->workSheet->getCell('FL1')->getValue();
        $this->countryCodes['FM'] = $this->workSheet->getCell('FM1')->getValue();
        $this->countryCodes['FN'] = $this->workSheet->getCell('FN1')->getValue();
        $this->countryCodes['FO'] = $this->workSheet->getCell('FO1')->getValue();
        $this->countryCodes['FP'] = $this->workSheet->getCell('FP1')->getValue();
        $this->countryCodes['FQ'] = $this->workSheet->getCell('FQ1')->getValue();
        $this->countryCodes['FR'] = $this->workSheet->getCell('FR1')->getValue();
        $this->countryCodes['FS'] = $this->workSheet->getCell('FS1')->getValue();
        $this->countryCodes['FT'] = $this->workSheet->getCell('FT1')->getValue();
        $this->countryCodes['FU'] = $this->workSheet->getCell('FU1')->getValue();
        $this->countryCodes['FV'] = $this->workSheet->getCell('FV1')->getValue();
        $this->countryCodes['FW'] = $this->workSheet->getCell('FW1')->getValue();
        $this->countryCodes['FX'] = $this->workSheet->getCell('FX1')->getValue();
        $this->countryCodes['FY'] = $this->workSheet->getCell('FY1')->getValue();
        $this->countryCodes['FZ'] = $this->workSheet->getCell('FZ1')->getValue();
        $this->countryCodes['GA'] = $this->workSheet->getCell('GA1')->getValue();
        $this->countryCodes['GB'] = $this->workSheet->getCell('GB1')->getValue();
        $this->countryCodes['GC'] = $this->workSheet->getCell('GC1')->getValue();
        $this->countryCodes['GD'] = $this->workSheet->getCell('GD1')->getValue();
        $this->countryCodes['GE'] = $this->workSheet->getCell('GE1')->getValue();
        $this->countryCodes['GF'] = $this->workSheet->getCell('GF1')->getValue();
        $this->countryCodes['GG'] = $this->workSheet->getCell('GG1')->getValue();
        $this->countryCodes['GH'] = $this->workSheet->getCell('GH1')->getValue();
        $this->countryCodes['GI'] = $this->workSheet->getCell('GI1')->getValue();
        $this->countryCodes['GJ'] = $this->workSheet->getCell('GJ1')->getValue();
        $this->countryCodes['GK'] = $this->workSheet->getCell('GK1')->getValue();
        $this->countryCodes['GL'] = $this->workSheet->getCell('GL1')->getValue();
        $this->countryCodes['GM'] = $this->workSheet->getCell('GM1')->getValue();
        $this->countryCodes['GN'] = $this->workSheet->getCell('GN1')->getValue();
        $this->countryCodes['GO'] = $this->workSheet->getCell('GO1')->getValue();
        $this->countryCodes['GP'] = $this->workSheet->getCell('GP1')->getValue();
        $this->countryCodes['GQ'] = $this->workSheet->getCell('GQ1')->getValue();
        $this->countryCodes['GR'] = $this->workSheet->getCell('GR1')->getValue();
        $this->countryCodes['GS'] = $this->workSheet->getCell('GS1')->getValue();
        $this->countryCodes['GT'] = $this->workSheet->getCell('GT1')->getValue();
        $this->countryCodes['GU'] = $this->workSheet->getCell('GU1')->getValue();
        $this->countryCodes['GV'] = $this->workSheet->getCell('GV1')->getValue();
        $this->countryCodes['GW'] = $this->workSheet->getCell('GW1')->getValue();
        $this->countryCodes['GX'] = $this->workSheet->getCell('GX1')->getValue();
        $this->countryCodes['GY'] = $this->workSheet->getCell('GY1')->getValue();
        $this->countryCodes['GZ'] = $this->workSheet->getCell('GZ1')->getValue();
        $this->countryCodes['HA'] = $this->workSheet->getCell('HA1')->getValue();
        $this->countryCodes['HB'] = $this->workSheet->getCell('HB1')->getValue();
        $this->countryCodes['HC'] = $this->workSheet->getCell('HC1')->getValue();
        $this->countryCodes['HD'] = $this->workSheet->getCell('HD1')->getValue();
        $this->countryCodes['HE'] = $this->workSheet->getCell('HE1')->getValue();
        $this->countryCodes['HF'] = $this->workSheet->getCell('HF1')->getValue();
        $this->countryCodes['HG'] = $this->workSheet->getCell('HG1')->getValue();
        $this->countryCodes['HH'] = $this->workSheet->getCell('HH1')->getValue();
        $this->countryCodes['HI'] = $this->workSheet->getCell('HI1')->getValue();
        $this->countryCodes['HJ'] = $this->workSheet->getCell('HJ1')->getValue();
        $this->countryCodes['HK'] = $this->workSheet->getCell('HK1')->getValue();
        $this->countryCodes['HL'] = $this->workSheet->getCell('HL1')->getValue();
        $this->countryCodes['HM'] = $this->workSheet->getCell('HM1')->getValue();
        $this->countryCodes['HN'] = $this->workSheet->getCell('HN1')->getValue();
        $this->countryCodes['HO'] = $this->workSheet->getCell('HO1')->getValue();
        $this->countryCodes['HP'] = $this->workSheet->getCell('HP1')->getValue();
        $this->countryCodes['HQ'] = $this->workSheet->getCell('HQ1')->getValue();
        $this->countryCodes['HR'] = $this->workSheet->getCell('HR1')->getValue();
        $this->countryCodes['HS'] = $this->workSheet->getCell('HS1')->getValue();
        $this->countryCodes['HT'] = $this->workSheet->getCell('HT1')->getValue();
        $this->countryCodes['HU'] = $this->workSheet->getCell('HU1')->getValue();
        $this->countryCodes['HV'] = $this->workSheet->getCell('HV1')->getValue();
        $this->countryCodes['HW'] = $this->workSheet->getCell('HW1')->getValue();
        $this->countryCodes['HX'] = $this->workSheet->getCell('HX1')->getValue();
        $this->countryCodes['HY'] = $this->workSheet->getCell('HY1')->getValue();
        $this->countryCodes['HZ'] = $this->workSheet->getCell('HZ1')->getValue();
        $this->countryCodes['IA'] = $this->workSheet->getCell('IA1')->getValue();
        $this->countryCodes['IB'] = $this->workSheet->getCell('IB1')->getValue();
        $this->countryCodes['IC'] = $this->workSheet->getCell('IC1')->getValue();
        $this->countryCodes['ID'] = $this->workSheet->getCell('ID1')->getValue();
        $this->countryCodes['IE'] = $this->workSheet->getCell('IE1')->getValue();
        $this->countryCodes['IF'] = $this->workSheet->getCell('IF1')->getValue();
        $this->countryCodes['IG'] = $this->workSheet->getCell('IG1')->getValue();
        $this->countryCodes['IH'] = $this->workSheet->getCell('IH1')->getValue();
        $this->countryCodes['II'] = $this->workSheet->getCell('II1')->getValue();
        $this->countryCodes['IJ'] = $this->workSheet->getCell('IJ1')->getValue();
        $this->countryCodes['IK'] = $this->workSheet->getCell('IK1')->getValue();
        $this->countryCodes['IL'] = $this->workSheet->getCell('IL1')->getValue();
        $this->countryCodes['IM'] = $this->workSheet->getCell('IM1')->getValue();
        $this->countryCodes['IN'] = $this->workSheet->getCell('IN1')->getValue();
        $this->countryCodes['IO'] = $this->workSheet->getCell('IO1')->getValue();
        $this->countryCodes['IP'] = $this->workSheet->getCell('IP1')->getValue();
        $this->countryCodes['IQ'] = $this->workSheet->getCell('IQ1')->getValue();
        $this->countryCodes['IR'] = $this->workSheet->getCell('IR1')->getValue();
        $this->countryCodes['IS'] = $this->workSheet->getCell('IS1')->getValue();
        $this->countryCodes['IT'] = $this->workSheet->getCell('IT1')->getValue();
        $this->countryCodes['IU'] = $this->workSheet->getCell('IU1')->getValue();
        $this->countryCodes['IV'] = $this->workSheet->getCell('IV1')->getValue();
        $this->countryCodes['IW'] = $this->workSheet->getCell('IW1')->getValue();
        $this->countryCodes['IX'] = $this->workSheet->getCell('IX1')->getValue();
        $this->countryCodes['IY'] = $this->workSheet->getCell('IY1')->getValue();
        $this->countryCodes['IZ'] = $this->workSheet->getCell('IZ1')->getValue();










        
    }

    public function readRatesFromFile()
    {
        $limit = 49;
        foreach ($this->countryCodes as $cell => $countryCodes) {
           $countryId = Country::where('name', ucwords(strtolower($countryCodes)))->value('id');
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
                    $newRates->shipping_service_id = $this->shippingService->id;
                    $newRates->country_id = $countryId;
                    $newRates->data = $rates;
                    $newRates->save();
                } else {
                    Rate::where('id', $checkrates->id)
                    ->update([
                        'data' => $rates
                     ]);
                }                
            }
        }
        return true;
    }

}