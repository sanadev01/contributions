<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportedOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imported_orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->bigInteger('import_id');
            $table->bigInteger('shipping_service_id')->nullable();
            $table->string('shipping_service_name')->nullable();
            $table->string('merchant')->nullable();
            $table->string('carrier')->nullable();
            $table->string('tracking_id')->nullable();
            $table->string('customer_reference')->nullable();

            $table->double('weight')->nullable();
            $table->double('length')->nullable();
            $table->double('width')->nullable();
            $table->double('height')->nullable();
            $table->boolean('is_shipment_added')->default(false);
            $table->string('measurement_unit',10)->default('kg/cm')->comment('kg/cm,lbs/in');
            $table->string('is_invoice_created')->nullable();
            $table->string('status')->nullable();
            $table->string('order_date')->nullable();

            $table->string('sender_first_name')->nullable();
            $table->string('sender_last_name')->nullable();
            $table->string('sender_email')->nullable();
            $table->string('sender_phone')->nullable();

            $table->bigInteger('state_id')->nullable();
            $table->bigInteger('country_id')->nullable();

            $table->string('user_declared_freight')->nullable();
            $table->longText('recipient')->nullable();
            $table->longText('items')->nullable();

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
        Schema::dropIfExists('imported_orders');
    }
}
