<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSinerlogFieldsOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('sinerlog_tran_id',191)->nullable()->after('api_response');
            $table->double('sinerlog_freight')->nullable()->after('sinerlog_tran_id');
            $table->string('sinerlog_url_label',500)->nullable()->after('sinerlog_freight');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('sinerlog_tran_id');
            $table->dropColumn('sinerlog_freight');
            $table->dropColumn('sinerlog_url_label');
        });
    }
}
