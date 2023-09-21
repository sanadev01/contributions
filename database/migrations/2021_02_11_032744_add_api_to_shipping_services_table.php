<?php

use App\Models\ShippingService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApiToShippingServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipping_services', function (Blueprint $table) {
            $table->string('api')->default(ShippingService::API_CORREIOS)->comment(ShippingService::API_CORREIOS.",".ShippingService::API_LEVE);
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
            $table->dropColumn('api');
        });
    }
}
