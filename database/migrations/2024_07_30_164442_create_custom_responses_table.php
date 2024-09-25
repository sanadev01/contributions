<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomResponsesTable extends Migration
{
    public function up()
    {
        Schema::create('custom_responses', function (Blueprint $table) {
            $table->id();
            $table->string('batch_id')->nullable();
            $table->json('response');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('custom_responses');
    }
}

