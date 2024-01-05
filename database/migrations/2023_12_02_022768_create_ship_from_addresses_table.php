<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShipFromAddressesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('ship_from_addresses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('sale_order_id')
                ->constrained('sale_orders')
                ->cascadeOnDelete();

            $table->string('name')->nullable();
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('address_line3')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('district')->nullable();
            $table->string('state_or_region')->nullable();
            $table->string('municipality')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country_code')->nullable();
            $table->string('phone')->nullable();
            $table->string('address_type')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'sale_order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('ship_from_addresses');
    }
};
