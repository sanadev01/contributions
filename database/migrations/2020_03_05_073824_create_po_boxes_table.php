<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePoBoxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('po_boxes', function (Blueprint $table) {
            $table->id();
            $table->string('address');
            $table->string('city')->nullable();
            $table->string('zipcode')->nullable();
            $table->string('state_id')->nullable();
            $table->string('country_id')->nullable();
            $table->string('phone')->nullable();
            $table->string('extra_data')->nullable();
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
        Schema::dropIfExists('po_boxes');
    }
}
