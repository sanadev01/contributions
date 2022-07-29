<?php

use App\Models\Order;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParcelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info("Importing parcels");
        $parcels = $this->loadParcelsFromFile();
        $this->command->getOutput()->progressStart(count($parcels));
        DB::beginTransaction();

        try {
            foreach ($parcels as $parcel) {
                $this->command->getOutput()->progressAdvance();
                Order::create(
                    $parcel
                );
            }
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            $this->command->error($ex->getMessage());
        }
        $this->command->getOutput()->progressFinish();
        $this->command->info("Import Success Full");
    }

    public function loadParcelsFromFile()
    {
        $contents = \Storage::get('upgrade/parcels.json');
        return json_decode($contents,true);
    }
}
