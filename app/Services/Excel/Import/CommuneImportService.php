<?php

namespace App\Services\Excel\Import;

use App\Models\Region;
use App\Models\Commune;
use App\Services\Excel\AbstractImportService;

class CommuneImportService extends AbstractImportService
{
    public function __construct()
    {
        parent::__construct(
            storage_path('chile_communes.xlsx')
        );
    }

    public function handle() 
    {
        $this->importCommunes();
    }

    private function importCommunes()
    {
        Commune::truncate();

        $communes = collect();
        $regions = Region::get();

        foreach (range(2, 1671) as $row) 
        {
            foreach ($regions as $region) 
            {
                if ($region->name == $this->workSheet->getCell('A'.$row)->getValue()) 
                {
                    $communes->push([
                        'region_id' => $region->id,
                        'name' => $this->workSheet->getCell('B'.$row)->getValue(),
                        'code' => null,
                    ]);
                }
            }
        }

        foreach($communes->chunk(100) as $communesChunk){
            Commune::insert($communesChunk->toArray());
        }

    }
}
