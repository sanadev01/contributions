<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarketplacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('marketplaces', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name');
            $table->string('code', 4);
            $table->string('marketplace_id');
            $table->string('mws_domain');
            $table->string('amazon_url');
            $table->string('currency')->nullable();
            $table->string('timezone')->nullable();

            $table->string('region_code', 2);
            $table->string('region_name', 25);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('marketplaces');
    }
};
