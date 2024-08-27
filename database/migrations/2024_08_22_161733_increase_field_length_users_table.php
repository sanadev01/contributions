<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IncreaseFieldLengthUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('address')->change();
            $table->text('name')->change();
            $table->text('last_name')->change();
            $table->string('email', 255)->change();
            $table->text('phone')->change();
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
            $table->string('address', 255)->change();
            $table->string('name', 255)->change();
            $table->string('last_name', 255)->change();
            $table->string('email', 255)->change(); 
            $table->string('phone', 255)->change(); 
        });
    }
}
