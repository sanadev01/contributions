<?php

use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Seeder;

class StatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Wiping Table');
        State::query()->truncate();

        $this->command->info('Seeding States...');

        $states = \Illuminate\Support\Facades\Storage::get('public/states.json');
        $states = json_decode($states);

        $current = null;

        foreach ($states as $state) {
            $country = Country::where('code', $state->country_code)->first();
            if (! $country) {
                continue;
            }

            if ($current != $country->name) {
                $current = $country->name;
                $this->command->info('Seeding State of country..'.$country->name);
            }

            $country->states()->create([
                'name' => $state->name,
                'code' => $state->state_code,
            ]);
        }
    }
}
