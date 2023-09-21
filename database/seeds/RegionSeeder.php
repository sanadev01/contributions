<?php

use App\Models\Region;
use Illuminate\Database\Seeder;
use App\Services\Excel\Import\RegionImportService;
use App\Services\Excel\Import\USARegionImportService;
use App\Services\Excel\Import\ColombiaRegionImportService;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Region::truncate();
        $regionImportService = new RegionImportService();
        $regionImportService->handle();

        $colombiaRegionImportService = new ColombiaRegionImportService();
        $colombiaRegionImportService->handle();

        $usaRegionImportService = new USARegionImportService();
        $usaRegionImportService->handle();
    }
}
