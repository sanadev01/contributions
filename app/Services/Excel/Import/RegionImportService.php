<?php

namespace App\Services\Excel\Import;

use App\Models\Region;
use App\Services\Excel\AbstractImportService;

class RegionImportService extends AbstractImportService
{
    public function __construct()
    {
        parent::__construct(
            storage_path('chile_regions.xlsx')
        );
    }

    public function handle() 
    {
        $this->importRegions();
    }

    private function importRegions()
    {
        Region::truncate();

        $regions = collect();

        foreach (range(2, 22) as $row) 
        {
            $regions->push([
                'country_id' => 46,
                'state_id' => null,
                'name' => strtoupper(trim($this->workSheet->getCell('A'.$row)->getValue())),
                'code' => $this->workSheet->getCell('B'.$row)->getValue(),
            ]);
        }
        
        foreach($regions->chunk(100) as $regionsChunk){
            Region::insert($regionsChunk->toArray());
        }
    }
}
