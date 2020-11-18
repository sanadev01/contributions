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
                'slug' => 'edit_order',
                'description' => 'Edit Order Sender/ Recipient and Services'
            ],
            [
                'slug' => 'print_label',
                'description' => 'Print Label'
            ],
            [
                'slug' => 'import_excel',
                'description' => 'Can Import Order via Excel Sheet'
            ],
            [
                'slug' => 'view_addresses',
                'description' => 'View All Addresses',
            ],
            [
                'slug' => 'show_address',
                'description' => 'Show Single Address',
            ],
            [
                'slug' => 'create_address',
                'description' => 'Create New Address',
            ],
            [
                'slug' => 'edit_address',
                'description' => 'Edit Address',
            ],
            [
                'slug' => 'delete_address',
                'description' => 'Delete Address',
            ],
            [
                'slug' => 'view_handlingServices',
                'description' => 'View All Handling Services',
            ],
            [
                'slug' => 'show_handlingService',
                'description' => 'Show Single Handling Service',
            ],
            [
                'slug' => 'create_handlingService',
                'description' => 'Create New Handling Service',
            ],
            [
                'slug' => 'edit_handlingService',
                'description' => 'Edit Handling Service',
            ],
            [
                'slug' => 'delete_handlingService',
                'description' => 'Delete Handling Service',
            ],
            [
                'slug' => 'view_billingInformations',
                'description' => 'View All Billing Informations',
            ],
            [
                'slug' => 'show_billingInformation',
                'description' => 'Show Single Billing Information',
            ],
            [
                'slug' => 'create_billingInformation',
                'description' => 'Create New Billing Information',
            ],
            [
                'slug' => 'edit_billingInformation',
                'description' => 'Edit Billing Information',
            ],
            [
                'slug' => 'delete_billingInformation',
                'description' => 'Delete Billing Information',
            ],
            [
                'slug' => 'view_shippingServices',
                'description' => 'View All Shipping Services',
            ],
            [
                'slug' => 'show_shippingService',
                'description' => 'Show Single Shipping Service',
            ],
            [
                'slug' => 'create_shippingService',
                'description' => 'Create New Shipping Service',
            ],
            [
                'slug' => 'edit_shippingService',
                'description' => 'Edit Shipping Service',
            ],
            [
                'slug' => 'delete_shippingService',
                'description' => 'Delete Shipping Service',
            ],
            [
                'slug' => 'view_profitPacakges',
                'description' => 'View All Profit Packages',
            ],
            [
                'slug' => 'show_profitPacakge',
                'description' => 'Show Single Profit Package',
            ],
            [
                'slug' => 'create_profitPacakge',
                'description' => 'Create New Profit Package',
            ],
            [
                'slug' => 'edit_profitPacakge',
                'description' => 'Edit Profit Package',
            ], 
            [
                'slug' => 'delete_profitPacakge',
                'description' => 'Delete Profit Package',
            ],
            [
                'slug' => 'view_rates',
                'description' => 'View All Rates',
            ],
            [
                'slug' => 'show_rate',
                'description' => 'Show Single Rate',
            ],
            [
                'slug' => 'create_rate',
                'description' => 'Create New Rate',
            ],
            [
                'slug' => 'edit_rate',
                'description' => 'Edit Rate',
            ],
            [
                'slug' => 'delete_rate',
                'description' => 'Delete Rate',
            ],
            [
                'slug' => 'view_settings',
                'description' => 'View All Settings',
            ],
            [
                'slug' => 'show_setting',
                'description' => 'Show Single Setting',
            ],
            [
                'slug' => 'create_setting',
                'description' => 'Create New Setting',
            ],
            [
                'slug' => 'edit_setting',
                'description' => 'Edit Setting',
            ],
            [
                'slug' => 'delete_setting',
                'description' => 'Delete Setting',
            ],
            [
                'slug' => 'view_users',
                'description' => 'View All Users',
            ],
            [
                'slug' => 'show_user',
                'description' => 'Show Single User',
            ],
            [
                'slug' => 'create_user',
                'description' => 'Create New User',
            ],
            [
                'slug' => 'edit_user',
                'description' => 'Edit User',
            ],
            [
                'slug' => 'delete_user',
                'description' => 'Delete User',
            ],
            [
                'slug' => 'view_roles',
                'description' => 'View All Roles',
            ],
            [
                'slug' => 'show_role',
                'description' => 'Show Single Role',
            ],
            [
                'slug' => 'create_role',
                'description' => 'Create New Role',
            ],
            [
                'slug' => 'edit_role',
                'description' => 'Edit Role',
            ],
            [
                'slug' => 'delete_role',
                'description' => 'Delete Role',
            ],
            [
                'slug' => 'view_tickets',
                'description' => 'View All Tickets',
            ],
            [
                'slug' => 'show_ticket',
                'description' => 'Show Single Ticket',
            ],
            [
                'slug' => 'create_ticket',
                'description' => 'Create New Ticket',
            ],
            [
                'slug' => 'edit_ticket',
                'description' => 'Edit Ticket',
            ],
            [
                'slug' => 'delete_ticket',
                'description' => 'Delete Ticket',
            ],
            [
                'slug' => 'view_connects',
                'description' => 'List All Integrations',
            ],
            [
                'slug' => 'create_connect',
                'description' => 'Create Integrations',
            ],
            [
                'slug' => 'delete_connect',
                'description' => 'Delete Integrations',
            ],
            [
                'slug' => 'order_report',
                'description' => 'Order Report Download',
            ],
            
        ];
    }
}
