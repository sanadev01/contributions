<?php

namespace App\Services\Excel\ImportCharges;

use App\Models\Rate;
use Illuminate\Http\UploadedFile;
use App\Services\Excel\AbstractImportService;

class ImportBPSCharges extends AbstractImportService
{
    public function __construct(UploadedFile $file)
    {
        $this->setService(self::SERVICE_BPS);

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
        $BpsRates = [];

        foreach (range(3, 70) as $row) {
            $BpsRates[] = [
                'weight' => $this->workSheet->getCell('A'.$row)->getValue(),
                'bps' => $this->workSheet->getCell('C'.$row)->getValue(),
                'leve' => $this->workSheet->getCell('D'.$row)->getValue()
            ];
        }

        // $row = 16;

        // $data = [
        //     'rates' => $BpsRates,
        //     'additional_kg' => $this->workSheet->getCell('C'.$row)->getValue(),
        //     'minimum_size' => $this->workSheet->getCell('C'.(++$row))->getValue(),
        //     'max_combine_dim' => $this->workSheet->getCell('C'.(++$row))->getValue(),
        //     'max_single_dim' => $this->workSheet->getCell('C'.(++$row))->getValue(),
        //     'max_weight' => $this->workSheet->getCell('C'.(++$row))->getValue(),
        //     'max_value' => $this->workSheet->getCell('C'.(++$row))->getValue(),
        // ];

        return $this->storeRatesToDb($BpsRates);
    }

    private function storeRatesToDb(array $data)
    {
        // $rates = $data['rates'];
        // unset($data['rates']);

        $bpsRates = Rate::first() ?? new Rate();

        $bpsRates->shipping_service_id = $_POST['shipping_service_id'];
        $bpsRates->country_id = $_POST['country_id'];
        $bpsRates->data = $data;
        $bpsRates->save();

        // $bpsRates->updateExtraArray(
        //     $data
        // );

        return $bpsRates;
    }
}
