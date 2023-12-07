<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAmazonColumnsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(0);
            $table->foreignId('parent_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('seller_id')->nullable();
            $table->foreignId('marketplace_id')->nullable()->constrained('marketplaces')->cascadeOnDelete();
            $table->string('region_code', 2)->nullable();
            $table->string('delete_status')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_active');
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
            $table->dropColumn('seller_id');
            $table->dropForeign(['marketplace_id']);
            $table->dropColumn('marketplace_id');
            $table->dropColumn('region_code');
            $table->dropColumn('delete_status');
        });
    }
}

