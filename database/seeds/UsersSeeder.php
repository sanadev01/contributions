<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info("Clearing users Table Table");
        DB::table('users')->truncate();

        $this->command->info("Importing users");
        $users = $this->loadUserssFromFile();
        $this->command->getOutput()->progressStart(count($users));
        foreach ($users as $user) {
            try {
                $this->command->getOutput()->progressAdvance();
                User::create(
                    $user
                );

            } catch (\Exception $ex) {
                $this->command->error($ex->getMessage());
            }
        }
        $this->command->getOutput()->progressFinish();
        $this->command->info("Import Success Full");

    }

    public function loadUserssFromFile()
    {
        $contents = Storage::get('upgrade/users.json');
        return json_decode($contents,true);
    }
}
