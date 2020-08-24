<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShippingServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipping_services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('max_length_allowed');
            $table->integer('max_width_allowed');
            $table->integer('min_width_allowed');
            $table->integer('min_length_allowed');
            $table->integer('max_sum_of_all_sides');
            $table->integer('contains_battery_charges');
            $table->integer('contains_perfume_charges');
            $table->integer('contains_flammable_liquid_charges');
            $table->tinyInteger('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipping_services');
    }
}
