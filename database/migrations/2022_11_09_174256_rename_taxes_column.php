<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameTaxesColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('taxes', function (Blueprint $table) {
            $table->renameColumn('tax_1', 'buying_usd');
            $table->renameColumn('tax_2', 'selling_usd');
            $table->renameColumn('tax_1_br', 'buying_br');
            $table->renameColumn('tax_2_br', 'selling_br');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('taxes', function (Blueprint $table) {
            $table->renameColumn('buying_usd', 'tax_1');
            $table->renameColumn('selling_usd', 'tax_2');
            $table->renameColumn('buying_br', 'tax_1_br');
            $table->renameColumn('selling_br', 'tax_2_br');
        });
    }
}
