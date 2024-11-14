<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProductIdToAmazonProductIdInSaleOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sale_order_items', function (Blueprint $table) {
            // Drop the foreign key constraint on 'product_id'
            $table->dropForeign(['product_id']);
            
            // Rename the column from 'product_id' to 'amazon_product_id'
            $table->renameColumn('product_id', 'amazon_product_id');

            // Add a new foreign key constraint on 'amazon_product_id'
            $table->foreign('amazon_product_id')
                  ->references('id')
                  ->on('amazon_products')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_order_items', function (Blueprint $table) {
            // Drop the foreign key constraint on 'amazon_product_id'
            $table->dropForeign(['amazon_product_id']);

            // Rename the column back to 'product_id'
            $table->renameColumn('amazon_product_id', 'product_id');

            // Re-add the original foreign key constraint on 'product_id'
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->cascadeOnDelete();
        });
    }
}
