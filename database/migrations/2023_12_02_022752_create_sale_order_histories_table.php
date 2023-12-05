<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleOrderHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('sale_order_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamp('from_date')->nullable();
            $table->timestamp('to_date')->nullable();
            $table->timestamp('last_update_till')->nullable();
            $table->unsignedInteger('execution_time')->default(0);
            $table->string('status');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('sale_order_histories');
    }
};
