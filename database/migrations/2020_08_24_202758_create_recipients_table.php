<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecipientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recipients', function (Blueprint $table) {
            $table->id();
            
            $table->bigInteger('order_id')->unique();
            $table->bigInteger('state_id')->nullable();
            $table->bigInteger('country_id')->nullable();

            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('city')->nullable();
            $table->string('street_no')->nullable();
            $table->longText('address')->nullable();
            $table->longText('address2')->nullable();
            $table->string('account_type')->nullable()->comment('individual,business');
            $table->string('tax_id')->nullable()->comment('cpf/cnpj/cnic');
            $table->string('zipcode')->nullable();
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
        Schema::dropIfExists('recipients');
    }
}
