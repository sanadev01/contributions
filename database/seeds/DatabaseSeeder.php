<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PermissionSeeder::class);
        $this->call(DefaultRoleSeeder::class);
        $this->call(AdminTableSeeder::class);

        $this->call(CountryTableSeeder::class);
        $this->call(StatesSeeder::class);
        $this->call(MarketplaceTableSeeder::class);

    }
}
