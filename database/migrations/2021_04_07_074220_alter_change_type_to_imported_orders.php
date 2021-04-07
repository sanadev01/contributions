<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterChangeTypeToImportedOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = [
            "activity_log",
            "addresses",
            "affiliate_sales",
            "api_logs",
            "billing_information",
            "cache",
            "commission_settings",
            "connects",
            "countries",
            "deposit_order",
            "deposits",
            "document_order",
            "document_ticket_comment",
            "documents",
            "failed_jobs",
            "handling_services",
            "import_orders",
            "imported_orders",
            "migrations",
            "order_items",
            "order_orders",
            "order_payment_invoice",
            "order_services",
            "orders",
            "password_resets",
            "payment_invoices",
            "permission_role",
            "permissions",
            "po_boxes",
            "profit_packages",
            "rates",
            "recipients",
            "roles",
            "sessions",
            "settings",
            "sh_codes",
            "shipping_services",
            "states",
            "ticket_comments",
            "tickets",
            "transactions",
            "users",
            "zip_codes"
        ];
        foreach ($tables as $table) {
            DB::statement('ALTER TABLE ' . $table . ' ENGINE = InnoDB');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = [
            "activity_log",
            "addresses",
            "affiliate_sales",
            "api_logs",
            "billing_information",
            "cache",
            "commission_settings",
            "connects",
            "countries",
            "deposit_order",
            "deposits",
            "document_order",
            "document_ticket_comment",
            "documents",
            "failed_jobs",
            "handling_services",
            "import_orders",
            "imported_orders",
            "migrations",
            "order_items",
            "order_orders",
            "order_payment_invoice",
            "order_services",
            "orders",
            "password_resets",
            "payment_invoices",
            "permission_role",
            "permissions",
            "po_boxes",
            "profit_packages",
            "rates",
            "recipients",
            "roles",
            "sessions",
            "settings",
            "sh_codes",
            "shipping_services",
            "states",
            "ticket_comments",
            "tickets",
            "transactions",
            "users",
            "zip_codes"
        ];
        foreach ($tables as $table) {
            DB::statement('ALTER TABLE ' . $table . ' ENGINE = MyISAM');
        }
    }
}
