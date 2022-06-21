<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommissionToAccrualRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accrual_rates', function (Blueprint $table) {
            $table->double('commission')->default(0)->after('gru');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accrual_rates', function (Blueprint $table) {
            $table->dropColumn('commission');
        });
    }
}
