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

            $table->bigInteger('user_id');
            $table->bigInteger('recipient_address_id')->nullable();

            $table->string('sender_first_name')->nullable();
            $table->string('sender_last_name')->nullable();
            $table->string('sender_email')->nullable();
            $table->string('sender_phone')->nullable();
            $table->string('sender_taxId')->nullable();
            
            $table->string('customer_reference')->nullable();
            $table->string('corrios_tracking_code')->nullable();
            $table->string('merchant')->nullable();
            $table->string('carrier')->nullable();
            $table->string('tracking_id')->nullable();
            $table->timestamp('order_date')->nullable();
            
            $table->double('weight')->default(0);
            $table->double('length')->default(0);
            $table->double('width')->default(0);
            $table->double('height')->nullable();
            $table->boolean('is_shipment_added')->default(false);
            $table->string('measurement_unit',10)->default('kg/cm')->comment('kg/cm,lbs/in');
            $table->bigInteger('purchase_invoice')->nullable();
            
            $table->string('warehouse_number',20)->nullable();
            $table->double('order_value')->default(0);
            $table->double('shipping_value')->default(0);
            $table->double('comission')->default(0);
            $table->double('dangrous_goods')->default(0);
            $table->double('total')->default(0);
            $table->double('discount')->default(0);
            $table->double('gross_total')->default(0);
            $table->double('insurance_value')->default(0);

            $table->bigInteger('shipping_service_id')->nullable();
            $table->string('shipping_service_name')->nullable();

            
            $table->double('user_declared_freight')->default(0);
            
            $table->string('tax_modality')->default('DDU')->comment('DDU,DDP');
            $table->boolean('is_invoice_created')->default(false);
            $table->string('status')->nullable();
            $table->boolean('is_consolidated')->default(false);
            $table->boolean('is_paid')->default(false);
            $table->boolean('is_received_from_sender')->default(false);
            $table->text('error')->nullable();

            $table->text('cn23')->nullable();

            $table->softDeletes();
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
