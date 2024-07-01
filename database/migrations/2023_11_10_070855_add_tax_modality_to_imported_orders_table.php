<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaxModalityToImportedOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('imported_orders', function (Blueprint $table) {
            $table->string('tax_modality')->default('DDU')->comment('DDU,DDP');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('imported_orders', function (Blueprint $table) {
            $table->dropColumn('tax_modality');
        });
    }
}
