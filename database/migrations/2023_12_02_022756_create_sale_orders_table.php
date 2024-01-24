<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleOrdersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('sale_orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('marketplace_id')
                ->constrained('marketplaces')
                ->cascadeOnDelete();

            $table->string('amazon_order_id')->index();
            $table->string('seller_order_id')->nullable();

            $table->timestamp('purchase_date')->nullable();
            $table->timestamp('last_update_date')->nullable();

            $table->string('order_status')->nullable();
            $table->string('fulfillment_channel')->nullable();
            $table->string('sales_channel')->nullable();
            $table->string('order_channel')->nullable();
            $table->string('ship_service_level')->nullable();

            $table->double('order_total')->nullable();
            $table->integer('number_of_items_shipped')->nullable();
            $table->integer('number_of_items_unshipped')->nullable();

            $table->string('payment_execution_detail')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_method_details')->nullable();
            $table->string('shipment_service_level_category')->nullable();
            $table->string('easy_ship_shipment_status')->nullable();
            $table->string('cba_displayable_shipping_label')->nullable();

            $table->string('order_type')->nullable();
            $table->timestamp('earliest_ship_date')->nullable();
            $table->timestamp('latest_ship_date')->nullable();
            $table->timestamp('earliest_delivery_date')->nullable();
            $table->timestamp('latest_delivery_date')->nullable();

            $table->boolean('is_business_order')->nullable();
            $table->boolean('is_prime')->nullable();
            $table->boolean('is_premium_order')->nullable();
            $table->boolean('is_global_express_enabled')->nullable();

            $table->boolean('is_replacement_order')->nullable();
            $table->string('replaced_order_id')->nullable();

            $table->timestamp('promise_response_due_date')->nullable();
            $table->boolean('is_estimated_ship_date_set')->nullable();
            $table->boolean('is_sold_by_ab')->nullable();

            $table->string('fulfillment_instruction')->nullable();
            $table->boolean('is_ispu')->default(0);

            $table->timestamps();

            $table->unique(['user_id', 'amazon_order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('sale_orders');
    }
};
