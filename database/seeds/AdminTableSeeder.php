<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::admin()->first();
        if ( !$user ){
            User::create([
                'role_id' => User::ROLE_ADMIN,
                'name' => 'admin',
                'email' => 'admin@admin.com',
                'password' => bcrypt('12345678'),
            ]);
        }
    }
}
