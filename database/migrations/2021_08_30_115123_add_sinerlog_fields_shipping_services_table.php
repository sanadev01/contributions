<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSinerlogFieldsShippingServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipping_services', function (Blueprint $table) {
            $table->double('max_sum_of_all_products')->nullable()->after('delivery_time');
            $table->string('service_api_alias',45)->nullable()->after('max_sum_of_all_products');
            $table->double('min_height_allowed')->nullable()->after('service_api_alias');
            $table->double('max_height_allowed')->nullable()->after('min_height_allowed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipping_services', function (Blueprint $table) {
            $table->dropColumn('max_sum_of_all_products');
            $table->dropColumn('service_api_alias');
            $table->dropColumn('min_height_allowed');
            $table->dropColumn('max_height_allowed');
        });
    }
}
