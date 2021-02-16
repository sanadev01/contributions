<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('name')->nullable();
            $table->double('price')->default(0);
            $table->string('sku')->nullable();
            $table->string('status')->nullable();
            $table->longText('description')->nullable();
            $table->integer('quantity')->nullable();

            $table->string('merchant')->nullable();
            $table->string('sh_code')->nullable();
            $table->string('carrier')->nullable();
            $table->string('tracking_id')->nullable();
            $table->timestamp('order_date')->nullable();

            $table->double('weight')->default(0);
            $table->double('length')->default(0);
            $table->double('width')->default(0);
            $table->double('height')->nullable();
            $table->string('measurement_unit',10)->default('kg/cm')->comment('kg/cm,lbs/in');
            $table->bigInteger('invoice_file')->nullable();
            $table->string('warehouse_number',20)->nullable();
            
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
        Schema::dropIfExists('products');
    }
}
