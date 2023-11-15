<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ZonesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $zones = ['Zone 1', 'Zone 2', 'Zone 3', 'Zone 4', 'Zone 5', 'Zone 6', 'Zone 7', 'Zone 8'];

        foreach ($zones as $zone) {
            DB::table('zones')->insert([
                'name' => $zone,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
