<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZoneCountryTable extends Migration
{
    public function up()
    {
        Schema::create('zone_country', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('zone_id');
            $table->foreignId('shipping_service_id')->constrained();
            $table->foreignId('country_id')->constrained();
            $table->decimal('profit_percentage', 5, 2)->nullable();
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('zone_country');
    }
}

