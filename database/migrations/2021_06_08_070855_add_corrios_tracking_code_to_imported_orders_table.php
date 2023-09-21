<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCorriosTrackingCodeToImportedOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('imported_orders', function (Blueprint $table) {
            $table->string('correios_tracking_code',50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('imported_orders', function (Blueprint $table) {
            $table->dropColumn('correios_tracking_code');
        });
    }
}
