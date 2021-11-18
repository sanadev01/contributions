<?php

namespace App\Services\Excel\Import;

use App\Models\Region;
use App\Services\Excel\AbstractImportService;

class RegionImportService extends AbstractImportService
{
    public function __construct()
    {
        parent::__construct(
            storage_path('app/excels/chile_regions.xlsx')
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

        foreach (range(2, 1671) as $row) 
        {
            $regions->push([
                'country_id' => 46,
                'state_id' => null,
                'name' => $this->workSheet->getCell('A'.$row)->getValue(),
            ]);
        }

        $unique_regions = $regions->unique(function ($region) {
            return $region['name'];
        });

        foreach($unique_regions->chunk(100) as $regionsChunk){
            Region::insert($regionsChunk->toArray());
        }
    }
}
