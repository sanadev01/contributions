<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmazonProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amazon_products', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
                
            $table->foreignId('marketplace_id')
                ->constrained('marketplaces')
                ->cascadeOnDelete();
                
            $table->string('sku');
            $table->string('asin', 40)->nullable();
            $table->text('title')->nullable();
            
            $table->timestamps();
            
            $table->unique(['user_id', 'sku']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('amazon_products');
    }
}
