<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('last_name')->after('name');
            $table->string('city')->after('password');
            $table->integer('state_id')->after('city');
            $table->integer('country_id')->after('state_id');
            $table->string('street_no')->after('country_id');
            $table->longText('address')->after('street_no');
            $table->longText('address2')->after('address');
            $table->string('account_type')->after('address2');
            $table->integer('role_id')->after('account_type');
            $table->integer('cpf/cnpj/cnic')->after('role_id');
            $table->string('zipcode')->after('cpf/cnpj/cnic');
            $table->string('phone')->after('zipcode');
            $table->string('pobox_number')->after('phone');
            $table->string('package_id')->after('pobox_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['last_name', 'city', 'state_id', 'country_id', 'street_no', 'address', 'address2', 'account_type', 'role_id', 'cpf/cnpj/cnic', 'zipcode', 'phone', 'pobox_number', 'package_id']);
        });
    }
}
