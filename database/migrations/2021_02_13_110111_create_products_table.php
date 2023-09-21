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
            $table->bigInteger('sh_code')->nullable();
            $table->double('price')->default(0);
            $table->string('sku')->nullable();
            $table->string('status')->nullable();
            $table->longText('description')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('order')->nullable();
            $table->string('category')->nullable();
            $table->string('brand')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('barcode')->nullable();
            $table->string('item')->nullable();
            $table->string('lot')->nullable();
            $table->string('unit')->nullable();
            $table->string('case')->nullable();
            $table->string('inventory_value')->nullable();
            $table->integer('min_quantity')->nullable();
            $table->integer('max_quantity')->nullable();
            $table->string('discontinued')->nullable();
            $table->integer('store_day')->nullable();
            $table->string('location')->nullable();
            
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
