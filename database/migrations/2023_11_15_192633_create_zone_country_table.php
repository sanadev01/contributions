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
            $table->unsignedBigInteger('country_id');
            $table->decimal('profit_percentage', 5, 2); // Adjust the precision and scale as needed
            $table->timestamps();

            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('zone_country');
    }
}

