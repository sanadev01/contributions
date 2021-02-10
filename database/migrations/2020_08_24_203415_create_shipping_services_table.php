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
            $table->double('max_length_allowed')->nullable();
            $table->double('max_width_allowed')->nullable();
            $table->double('min_width_allowed')->nullable();
            $table->double('min_length_allowed')->nullable();
            $table->double('max_sum_of_all_sides')->nullable();
            $table->double('max_weight_allowed')->nullable();
            $table->double('contains_battery_charges')->default(0);
            $table->double('contains_perfume_charges')->default(0);
            $table->double('contains_flammable_liquid_charges')->default(0);
            $table->boolean('active')->default(true);
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
