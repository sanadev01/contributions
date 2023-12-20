<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('sale_order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sale_order_id')
                ->constrained('sale_orders')
                ->cascadeOnDelete();

            $table->string('order_item_id')->nullable();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->integer('quantity_ordered')->nullable();
            $table->integer('quantity_shipped')->nullable();
            $table->integer('number_of_items')->nullable();

            $table->double('item_price')->default(0)->nullable();
            $table->double('item_tax')->default(0)->nullable();

            $table->double('shipping_price')->default(0)->nullable();
            $table->double('shipping_tax')->default(0)->nullable();

            $table->double('gift_wrap_price')->default(0)->nullable();
            $table->double('gift_wrap_tax')->default(0)->nullable();
            $table->text('gift_message_text')->nullable();
            $table->string('gift_wrap_level')->nullable();

            $table->text('buyer_customized_info')->nullable();

            $table->double('shipping_discount')->default(0)->nullable();
            $table->double('shipping_discount_tax')->default(0)->nullable();

            $table->double('promotion_discount')->default(0)->nullable();
            $table->double('promotion_discount_tax')->default(0)->nullable();
            $table->text('promotion_ids')->nullable();

            $table->double('cod_fee')->default(0)->nullable();
            $table->double('cod_fee_discount')->default(0)->nullable();

            $table->boolean('is_gift')->nullable();
            $table->string('condition_note')->nullable();
            $table->string('condition_id')->nullable();
            $table->string('condition_sub_type_id')->nullable();

            $table->timestamp('scheduled_delivery_start_date')->nullable();
            $table->timestamp('scheduled_delivery_end_date')->nullable();

            $table->string('price_designation')->nullable();
            $table->boolean('serial_number_required')->nullable();
            $table->boolean('is_transparency')->nullable();

            $table->string('ioss_number')->nullable();
            $table->string('deemed_reseller_category')->nullable();

            $table->json('granted_points')->nullable();
            $table->json('tax_collection')->nullable();

            $table->timestamps();

            $table->unique(['sale_order_id', 'order_item_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('sale_order_items');
    }
};
