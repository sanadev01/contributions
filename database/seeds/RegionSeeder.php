<?php

use App\Models\Region;
use Illuminate\Database\Seeder;
use App\Services\Excel\Import\RegionImportService;
use App\Services\Excel\Import\USARegionImportService;

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

        $usaRegionImportService = new USARegionImportService();
        $usaRegionImportService->handle();
    }
}
