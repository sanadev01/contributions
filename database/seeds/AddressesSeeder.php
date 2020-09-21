<?php

use App\Models\Address;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class AddressesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info("Clearing Addresses Table");
        Address::truncate();
        $this->command->info("Importing Addresses");
        $addresses = $this->getAddressesFromFile();

        $this->command->getOutput()->progressStart(count($addresses));
        foreach ($addresses as $address) {
            $this->command->getOutput()->progressAdvance();
            Address::create($address);
        }
        $this->command->getOutput()->progressFinish();
        $this->command->info("Import Success Full");
        
    }

    public function getAddressesFromFile()
    {
        $contents = Storage::get('upgrade/addresses.json');
        return json_decode($contents,true);
    }
}
