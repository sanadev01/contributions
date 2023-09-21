<?php

use App\Models\Commune;
use Illuminate\Database\Seeder;
use App\Services\Excel\Import\CommuneImportService;

class CommuneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Commune::truncate();
        $communeImportService = new CommuneImportService();
        $communeImportService->handle();
    }
}
