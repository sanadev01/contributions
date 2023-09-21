<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DefaultRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ([
            User::ROLE_ADMIN => 'admin',
            2 => 'retailer'
        ] as $id => $role) {
            Role::create([
                'id' => $id,
                'name' => $role
            ]);
        }
    }
}
