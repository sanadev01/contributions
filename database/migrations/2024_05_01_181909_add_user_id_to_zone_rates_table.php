<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToZoneRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('zone_rates', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('shipping_service_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('zone_rates', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
}
