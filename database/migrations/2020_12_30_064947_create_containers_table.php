<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContainersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('containers', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('user_id');
            $table->string('seal_no');
            $table->string('dispatch_number');
            $table->string('tax_modality')->default('ddu')->comment('ddu,dpp');
            $table->string('origin_country')->default('US');
            $table->string('origin_operator_name')->default('HERC');
            $table->string('destination_operator_name')->comment('SAOD-GRU, CRBA-CWB');
            $table->string('postal_category_code')->default('A');
            $table->string('services_subclass_code')->default('NX')->comment('NX -> Packet Standard , IX-> Packet Express');
            $table->text('unit_response_list')->nullable();
            $table->string('unit_code')->nullable();
            $table->bigInteger('sequence')->default(1);
            $table->tinyInteger('unit_type')->default(2)->comment('1- Bag, 2-Box');

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
        Schema::dropIfExists('containers');
    }
}
