<?php

namespace App\Services\Excel\ImportCharges;

use App\Models\Country;
use Illuminate\Http\UploadedFile;
use App\Services\Excel\AbstractImportService;

class ImportEPacketCharges extends AbstractImportService
{
    public function __construct(UploadedFile $file)
    {
        $filename = $this->importFile($file);

        parent::__construct(
            $this->getStoragePath($filename)
        );
    }

    public function handle()
    {
        $data = $this->readDataFromFile();
        return $this->storeDataToDB($data);
    }

    private function storeDataToDB(array $rows)
    {
        foreach ($rows as $row) {
            $row['country']->ePacketRates()->create([
                'rates' => $row['rates']
            ]);
        }
    }

    public function readDataFromFile()
    {
        $ePacketRates = [];

        foreach (range(3, 38) as $row) {
            $ePacketRates[] = [
                'rates' => array_combine(range(1, 68), $this->readRowAsArray($row, 'C', 'BS')),
                'country' => Country::whereCode($this->workSheet->getCell('B'.$row)->getValue())->first(),
                'code' => $this->workSheet->getCell('B'.$row)->getValue()
            ];
        }

        return $ePacketRates;
    }

    private function readRowAsArray($row, $startCell, $endCell)
    {
        $rates = [];
        for ($cell = $startCell; $cell != $endCell; $cell++) {
            $rates[] = $this->workSheet->getCell($cell.$row)->getValue();
        }

        return $rates;
    }
}
