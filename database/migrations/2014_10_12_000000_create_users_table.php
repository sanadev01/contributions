<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('pobox_number')->nullable()->unique();
            $table->bigInteger('package_id')->nullable();
            $table->bigInteger('state_id')->nullable();
            $table->bigInteger('country_id')->nullable();
            $table->bigInteger('role_id');

            $table->string('name');
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->string('city')->nullable();
            $table->string('street_no')->nullable();
            $table->longText('address')->nullable();
            $table->longText('address2')->nullable();
            $table->string('account_type')->nullable()->comment('individual,business');
            $table->string('tax_id')->nullable()->comment('cpf/cnpj/cnic');
            $table->string('zipcode')->nullable();

            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
