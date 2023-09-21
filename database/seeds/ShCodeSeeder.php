<?php

use App\Models\ShCode;
use App\Services\Excel\Import\NCMImportService;
use Illuminate\Database\Seeder;

class ShCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ShCode::truncate();
        $ncmImportService = new NCMImportService();
        $ncmImportService->handle();
    }
}
