<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuyerInfoTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('buyer_info', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('sale_order_id')
                ->constrained('sale_orders')
                ->cascadeOnDelete();

            $table->string('buyer_email')->nullable();
            $table->string('buyer_name')->nullable();
            $table->string('buyer_country')->nullable();

            $table->json('buyer_tax_info')->nullable();
            $table->string('purchase_order_number')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'sale_order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('buyer_info');
    }
};
