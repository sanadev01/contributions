<?php

use App\Models\BillingInformation;
use Illuminate\Database\Seeder;

class BillingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info("Clearing Bilings Table");
        BillingInformation::truncate();
        $this->command->info("Importing Bilings");
        $billings = $this->getBillingsFromFile();
        $this->command->getOutput()->progressStart(count($billings));
        foreach ($billings as $billing) {
            $this->command->getOutput()->progressAdvance();
            BillingInformation::create($billing);
        }
        $this->command->getOutput()->progressFinish();
        $this->command->info("Import Success Full");
    }

    public function getBillingsFromFile()
    {
        $contents = Storage::get('upgrade/billings.json');
        return json_decode($contents,true);
    }
}
