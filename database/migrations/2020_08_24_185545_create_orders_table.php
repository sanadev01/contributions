<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('merchant');
            $table->string('carrier');
            $table->string('tracking_id');
            $table->integer('weight');
            $table->string('order_date');
            $table->integer('length');
            $table->integer('width');
            $table->integer('height');
            $table->string('measurement_unit');
            $table->string('warehouse_number');
            $table->string('order_value');
            $table->string('shipping_value');
            $table->integer('shipping_service_id');
            $table->integer('shipping_service_name');
            $table->integer('recipient_address_id');
            $table->integer('user_id');
            $table->string('tax_modality');
            $table->integer('insurance_value');
            $table->tinyInteger('invoice_created');
            $table->string('status');
            $table->tinyInteger('is_consolidated');
            $table->tinyInteger('is_paid');
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
        Schema::dropIfExists('orders');
    }
}
