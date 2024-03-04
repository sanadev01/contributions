<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpTokensTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('sp_tokens', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->text('access_token');
            $table->text('refresh_token');
            $table->string('token_type')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_updated_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('sp_tokens');
    }
};
