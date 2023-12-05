<?php

namespace Database\Seeders;

use App\Models\Marketplace;
use Illuminate\Database\Seeder;

class MarketplaceTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     */
    public function run(): void {
        foreach (config('marketplaces') as $region => $marketplaces) {
            foreach ($marketplaces as $_marketplace) {
                $marketplace = Marketplace::query()
                    ->where('marketplace_id', $_marketplace['marketplace_id'])
                    ->orWhere('code', $_marketplace['code'])
                    ->first();

                if (!$marketplace) {
                    $marketplace = new Marketplace($_marketplace);
                }

                $marketplace->fill($_marketplace);
                $marketplace->save();
            }
        }
    }

}
