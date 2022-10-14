<?php

namespace App\Services\Excel\Import;

use App\Models\Region;
use App\Models\Country;
use App\Services\Excel\AbstractImportService;

class ColombiaRegionImportService extends AbstractImportService
{
    public function __construct()
    {
        parent::__construct(
            storage_path('colombia_updated_regions.xlsx')
        );
    }

    public function handle()
    {
        $this->importRegions();
    }

    private function importRegions()
    {
        $regions = collect();

        foreach (range(2, 1104) as $row)
        {
            $regions->push([
                'country_id' => Country::COLOMBIA,
                'state_id' => null,
                'name' => $this->getRegionTitle($row),
                'code' => (int)$this->workSheet->getCell('B'.$row)->getValue(),
            ]);
        }

        foreach ($regions->chunk(100) as $regionsChunk) {
            Region::insert($regionsChunk->toArray());
        }
    }

    private function getRegionTitle($row)
    {
        $title = strtoupper(trim($this->workSheet->getCell('A'.$row)->getValue()));

        return str_replace('_', ' ', $title);
    }
}
