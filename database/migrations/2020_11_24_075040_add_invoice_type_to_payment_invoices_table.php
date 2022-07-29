<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvoiceTypeToPaymentInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_invoices', function (Blueprint $table) {
            $table->string('type')->after('is_paid')->default('prepaid')->comment('prepaid,postpaid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_invoices', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
