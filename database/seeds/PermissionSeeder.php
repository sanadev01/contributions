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
                'group' => 'Parcel',
                'description' => 'View Details of An Parcel'
            ],
            [
                'slug' => 'create_parcel',
                'group' => 'Parcel',
                'description' => 'Create New Parcel'
            ],
            [
                'slug' => 'update_parcel',
                'group' => 'Parcel',
                'description' => 'Update Parcel'
            ],
            [
                'slug' => 'delete_parcel',
                'group' => 'Parcel',
                'description' => 'Delete Parcel'
            ],
            [
                'slug' => 'add_parcel_warehouse_number',
                'group' => 'Parcel',
                'description' => 'Add Warehouse Number to Parcel'
            ],
            [
                'slug' => 'add_parcel_shipment_details',
                'group' => 'Parcel',
                'description' => 'Add Dimensions, Pcitures and weight of Parcel'
            ],
            [
                'slug' => 'edit_parcel_shipment_details',
                'group' => 'Parcel',
                'description' => ''
            ],
            [
                'slug' => 'edit_order',
                'group' => 'Order',
                'description' => 'Edit Order Sender/ Recipient and Services'
            ],
            [
                'slug' => 'print_label',
                'group' => 'Order',
                'description' => 'Print Label'
            ],
            [
                'slug' => 'import_excel',
                'group' => 'Order',
                'description' => 'Can Import Order via Excel Sheet'
            ],
            [
                'slug' => 'view_addresses',
                'group' => 'Address',
                'description' => 'View All Addresses',
            ],
            [
                'slug' => 'show_address',
                'group' => 'Address',
                'description' => 'Show Single Address',
            ],
            [
                'slug' => 'create_address',
                'group' => 'Address',
                'description' => 'Create New Address',
            ],
            [
                'slug' => 'edit_address',
                'group' => 'Address',
                'description' => 'Edit Address',
            ],
            [
                'slug' => 'delete_address',
                'group' => 'Address',
                'description' => 'Delete Address',
            ],
            [
                'slug' => 'view_handlingServices',
                'group' => 'Handling Services',
                'description' => 'View All Handling Services',
            ],
            [
                'slug' => 'show_handlingService',
                'group' => 'Handling Services',
                'description' => 'Show Single Handling Service',
            ],
            [
                'slug' => 'create_handlingService',
                'group' => 'Handling Services',
                'description' => 'Create New Handling Service',
            ],
            [
                'slug' => 'edit_handlingService',
                'group' => 'Handling Services',
                'description' => 'Edit Handling Service',
            ],
            [
                'slug' => 'delete_handlingService',
                'group' => 'Handling Services',
                'description' => 'Delete Handling Service',
            ],
            [
                'slug' => 'view_billingInformations',
                'group' => 'Billing Information',
                'description' => 'View All Billing Informations',
            ],
            [
                'slug' => 'show_billingInformation',
                'group' => 'Billing Information',
                'description' => 'Show Single Billing Information',
            ],
            [
                'slug' => 'create_billingInformation',
                'group' => 'Billing Information',
                'description' => 'Create New Billing Information',
            ],
            [
                'slug' => 'edit_billingInformation',
                'group' => 'Billing Information',
                'description' => 'Edit Billing Information',
            ],
            [
                'slug' => 'delete_billingInformation',
                'group' => 'Billing Information',
                'description' => 'Delete Billing Information',
            ],
            [
                'slug' => 'view_shippingServices',
                'group' => 'Shipping Services',
                'description' => 'View All Shipping Services',
            ],
            [
                'slug' => 'show_shippingService',
                'group' => 'Shipping Services',
                'description' => 'Show Single Shipping Service',
            ],
            [
                'slug' => 'create_shippingService',
                'group' => 'Shipping Services',
                'description' => 'Create New Shipping Service',
            ],
            [
                'slug' => 'edit_shippingService',
                'group' => 'Shipping Services',
                'description' => 'Edit Shipping Service',
            ],
            [
                'slug' => 'delete_shippingService',
                'group' => 'Shipping Services',
                'description' => 'Delete Shipping Service',
            ],
            [
                'slug' => 'view_profitPacakges',
                'group' => 'Profit Package',
                'description' => 'View All Profit Packages',
            ],
            [
                'slug' => 'show_profitPacakge',
                'group' => 'Profit Package',
                'description' => 'Show Single Profit Package',
            ],
            [
                'slug' => 'create_profitPacakge',
                'group' => 'Profit Package',
                'description' => 'Create New Profit Package',
            ],
            [
                'slug' => 'edit_profitPacakge',
                'group' => 'Profit Package',
                'description' => 'Edit Profit Package',
            ], 
            [
                'slug' => 'delete_profitPacakge',
                'group' => 'Profit Package',
                'description' => 'Delete Profit Package',
            ],
            [
                'slug' => 'view_rates',
                'group' => 'Rates',
                'description' => 'View All Rates',
            ],
            [
                'slug' => 'show_rate',
                'group' => 'Rates',
                'description' => 'Show Single Rate',
            ],
            [
                'slug' => 'create_rate',
                'group' => 'Rates',
                'description' => 'Create New Rate',
            ],
            [
                'slug' => 'edit_rate',
                'group' => 'Rates',
                'description' => 'Edit Rate',
            ],
            [
                'slug' => 'delete_rate',
                'group' => 'Rates',
                'description' => 'Delete Rate',
            ],
            [
                'slug' => 'view_settings',
                'group' => 'Settings',
                'description' => 'View All Settings',
            ],
            [
                'slug' => 'show_setting',
                'group' => 'Settings',
                'description' => 'Show Single Setting',
            ],
            [
                'slug' => 'create_setting',
                'group' => 'Settings',
                'description' => 'Create New Setting',
            ],
            [
                'slug' => 'edit_setting',
                'group' => 'Settings',
                'description' => 'Edit Setting',
            ],
            [
                'slug' => 'delete_setting',
                'group' => 'Settings',
                'description' => 'Delete Setting',
            ],
            [
                'slug' => 'view_users',
                'group' => 'Users',
                'description' => 'View All Users',
            ],
            [
                'slug' => 'show_user',
                'group' => 'Users',
                'description' => 'Show Single User',
            ],
            [
                'slug' => 'create_user',
                'group' => 'Users',
                'description' => 'Create New User',
            ],
            [
                'slug' => 'edit_user',
                'group' => 'Users',
                'description' => 'Edit User',
            ],
            [
                'slug' => 'delete_user',
                'group' => 'Users',
                'description' => 'Delete User',
            ],
            [
                'slug' => 'view_roles',
                'group' => 'Roles',
                'description' => 'View All Roles',
            ],
            [
                'slug' => 'show_role',
                'group' => 'Roles',
                'description' => 'Show Single Role',
            ],
            [
                'slug' => 'create_role',
                'group' => 'Roles',
                'description' => 'Create New Role',
            ],
            [
                'slug' => 'edit_role',
                'group' => 'Roles',
                'description' => 'Edit Role',
            ],
            [
                'slug' => 'delete_role',
                'group' => 'Roles',
                'description' => 'Delete Role',
            ],
            [
                'slug' => 'view_tickets',
                'group' => 'Support Tickets',
                'description' => 'View All Tickets',
            ],
            [
                'slug' => 'show_ticket',
                'group' => 'Support Tickets',
                'description' => 'Show Single Ticket',
            ],
            [
                'slug' => 'create_ticket',
                'group' => 'Support Tickets',
                'description' => 'Create New Ticket',
            ],
            [
                'slug' => 'edit_ticket',
                'group' => 'Support Tickets',
                'description' => 'Edit Ticket',
            ],
            [
                'slug' => 'delete_ticket',
                'group' => 'Support Tickets',
                'description' => 'Delete Ticket',
            ],
            [
                'slug' => 'view_connects',
                'group' => 'Integrations',
                'description' => 'List All Integrations',
            ],
            [
                'slug' => 'create_connect',
                'group' => 'Integrations',
                'description' => 'Create Integrations',
            ],
            [
                'slug' => 'delete_connect',
                'group' => 'Integrations',
                'description' => 'Delete Integrations',
            ],
            [
                'slug' => 'order-report',
                'group' => 'Reports',
                'description' => 'Order Report Download',
            ],
            [
                'slug' => 'can_import_leve_orders',
                'group' => 'Order',
                'description' => 'Special Permission to Import Orders From Leve',
            ],
            [
                'slug' => 'can_create_post_paid_invoices',
                'group' => 'Payment Invoices',
                'description' => 'Allows User to create post paid invoices. User will be able to print label before payment',
            ],
            [
                'slug' => 'affiliate_sale',
                'group' => 'Affiliate Sales',
                'description' => 'Affiliate Sale',
            ],
            [
                'slug' => 'activity_log',
                'group' => 'activity',
                'description' => 'Show Activity Logs',
            ],
            /**
             * warehouse-operations
             */
            [
                'slug' => 'warehouse_operations',
                'group' => 'Warehouse',
                'description' => 'Warehouse Operations',
            ],
            [
                'slug' => 'wo_create_bag',
                'group' => 'Warehouse Bag',
                'description' => 'Warehouse Operations | Create Bag',
            ],
            [
                'slug' => 'wo_edit_bag',
                'group' => 'Warehouse Bag',
                'description' => 'Warehouse Operations | Edit Bag',
            ],
            [
                'slug' => 'wo_list_bag',
                'group' => 'Warehouse Bag',
                'description' => 'Warehouse Operations | List Bag',
            ],
            [
                'slug' => 'wo_delete_bag',
                'group' => 'Warehouse Bag',
                'description' => 'Warehouse Operations | Delete Bag',
            ],
            
        ];
    }
}
