<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSenderAddressToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('sender_address')->nullable()->after('sender_taxId');
            $table->string('sender_city')->nullable()->after('sender_last_name');
            $table->text('chile_response')->nullable()->after('cn23');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('sender_zipcode');
            $table->dropColumn('sender_city');
            $table->dropColumn('chile_response');
        });
    }
}
