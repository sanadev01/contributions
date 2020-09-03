<?php

use App\Models\Permission;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::query()->truncate();
        foreach ($this->permisions() as $permission) {
            Permission::create(
                $permission
            );
        }
    }

    private function permisions()
    {
        return [
            [
                'slug' => 'view_parcel',
                'description' => 'View Details of An Parcel'
            ],
            [
                'slug' => 'create_parcel',
                'description' => 'Create New Parcel'
            ],
            [
                'slug' => 'update_parcel',
                'description' => 'Update Parcel'
            ],
            [
                'slug' => 'delete_parcel',
                'description' => 'Delete Parcel'
            ],
            [
                'slug' => 'add_parcel_warehouse_number',
                'description' => 'Add Warehouse Number to Parcel'
            ],
            [
                'slug' => 'add_parcel_shipment_details',
                'description' => 'Add Dimensions, Pcitures and weight of Parcel'
            ],
            [
                'slug' => 'edit_parcel_shipment_details',
                'description' => ''
            ]
        ];
    }
}
