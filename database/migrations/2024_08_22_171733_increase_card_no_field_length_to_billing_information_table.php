<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IncreaseCardNoFieldLengthToBillingInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('billing_information', function (Blueprint $table) {
            $table->text('card_no')->change();
            $table->text('cvv')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('billing_information', function (Blueprint $table) {
            $table->string('card_no')->change(); // Revert to the original data type (e.g., string)
            $table->string('cvv')->change(); // Revert to the original data type (e.g., string)
        });
    }
    
}
