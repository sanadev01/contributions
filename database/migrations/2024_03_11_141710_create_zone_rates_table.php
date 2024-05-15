<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZoneRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zone_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shipping_service_id');
            $table->longText('cost_rates')->nullable();
            $table->longText('selling_rates')->nullable();
            $table->timestamps();

            $table->foreign('shipping_service_id')->references('id')->on('shipping_services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zone_rates');
    }
}
