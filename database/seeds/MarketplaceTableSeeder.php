<?php

use Illuminate\Database\Seeder;
use App\Models\Marketplace;
class MarketplaceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    /**
     * Run the database seeds.
     */
    public function run()
    {
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
