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
            ],
            [
                'view_addresses' => 'View All Addresses',
                'show_address' => 'Show Single Address',
                'create_address' => 'Create New Address',
                'edit_address' => 'Edit Address',
                'delete_address' => 'Delete Address',
            ],
            [
                'view_handlingServices' => 'View All Handling Services',
                'show_handlingService' => 'Show Single Handling Service',
                'create_handlingService' => 'Create New Handling Service',
                'edit_handlingService' => 'Edit Handling Service',
                'delete_handlingService' => 'Delete Handling Service',
            ]
        ];
    }
}
