<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameUspsInOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('corrios_usps_tracking_code', 'us_api_tracking_code');
            $table->renameColumn('usps_cost', 'us_api_cost');
            $table->renameColumn('usps_response', 'us_api_response');
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
            $table->renameColumn('us_api_tracking_code', 'corrios_usps_tracking_code');
            $table->renameColumn('us_api_cost', 'usps_cost');
            $table->renameColumn('us_api_response', 'usps_response');
        });
    }
}
